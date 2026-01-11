<?php
declare(strict_types=1);

namespace App\Migration;

use App\Config\AppConfig;
use App\Influx\InfluxClient;
use App\Logger\DebugLogger;
use App\Mysql\PfsenseLogRepository;
use App\Mysql\PortCountRepository;
use App\Parser\PfsenseMessageParser;

final class PfsenseSyslogMigrator
{
    private array $trackedPorts = [445, 5353, 5355];

    public function __construct(
        private AppConfig $config,
        private InfluxClient $influx,
        private PfsenseMessageParser $parser,
        private PfsenseLogRepository $logRepo,
        private PortCountRepository $portRepo,
        private DebugLogger $logger
    ) {}

    public function migrate(): bool
    {
        $offset = 0;
        $insertedTotal = 0;
        $portCounts = [];

        foreach ($this->trackedPorts as $p) {
            $portCounts[$p] = [];
        }

        while (true) {
            $data = $this->influx->query($this->buildFlux($offset));
            if (empty($data)) {
                break;
            }

            $parsed = [];

            foreach ($data as $row) {
                if (!isset($row['message'])) {
                    continue;
                }

                $record = $this->parser->parse($row['message'], $row['_time']);
                if (!$record) {
                    continue;
                }

                $parsed[] = $record;

                if (in_array($record['port_destination'], $this->trackedPorts, true)) {
                    $date = date('Y-m-d', strtotime($record['timestamp']));
                    $portCounts[$record['port_destination']][$date] =
                        ($portCounts[$record['port_destination']][$date] ?? 0) + 1;
                }
            }

            $insertedTotal += $this->logRepo->insertBatch($parsed);
            $offset += $this->config->batchSize();
        }

        $this->portRepo->update($portCounts);

        return $insertedTotal > 0;
    }

    private function buildFlux(int $offset): string
    {
        return sprintf(
            'from(bucket: "%s")
             |> range(start: 0)
             |> filter(fn: (r) => r._measurement == "pfsense_syslog")
             |> pivot(rowKey:["_time"], columnKey: ["_field"], valueColumn: "_value")
             |> sort(columns: ["_time"])
             |> limit(n: %d, offset: %d)',
            $this->config->influxBucket(),
            $this->config->batchSize(),
            $offset
        );
    }
}
