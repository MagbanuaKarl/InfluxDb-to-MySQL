<?php
declare(strict_types=1);

namespace App\Influx;

use App\Config\AppConfig;
use App\Logger\DebugLogger;
use RuntimeException;

final class InfluxClient
{
    public function __construct(
        private AppConfig $config,
        private DebugLogger $logger
    ) {}

    public function query(string $fluxQuery): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->config->influxUrl() . '/api/v2/query?org=' . urlencode($this->config->influxOrg()),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['query' => $fluxQuery]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Token ' . $this->config->influxToken(),
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 600
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new RuntimeException("InfluxDB query failed with HTTP {$httpCode}");
        }

        return InfluxResponseParser::parse($response);
    }
}
