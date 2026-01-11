<?php
declare(strict_types=1);

namespace App\Influx;

use App\Config\AppConfig;
use App\Logger\DebugLogger;
use Exception;

final class InfluxServiceManager
{
    public function __construct(
        private AppConfig $config,
        private DebugLogger $logger
    ) {}

    public function restart(): bool
    {
        echo "\n🔄 Restarting InfluxDB service...\n";

        try {
            $connection = ssh2_connect($this->config->influxHost(), 22);
            if (!$connection) {
                throw new Exception('SSH connection failed');
            }

            if (!ssh2_auth_password($connection, 'mis', 'MISADMIN42')) {
                throw new Exception('SSH authentication failed');
            }

            $stream = ssh2_exec($connection, 'sudo systemctl restart influxdb');
            stream_set_blocking($stream, true);
            stream_get_contents($stream);
            fclose($stream);

            echo "⏳ Waiting 5 minutes for InfluxDB restart...\n";
            sleep(300);

            return true;

        } catch (Exception $e) {
            $this->logger->logError('InfluxServiceManager::restart', $e);
            return false;
        }
    }
}
