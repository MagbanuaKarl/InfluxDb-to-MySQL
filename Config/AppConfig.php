<?php
declare(strict_types=1);

namespace App\Config;

/**
 * AppConfig handles centralized configuration for database connections
 * and application runtime settings.
 */
final class AppConfig
{
    /**
     * Returns the centralized Server IP address.
     * Update this value here to propagate changes across all connection strings.
     */
    private function serverIp(): string
    {
        return '192.X.X.XXX';
    }

    // =========================================================================
    // INFLUXDB CONFIGURATION
    // =========================================================================

    /**
     * Full API endpoint URL for InfluxDB.
     */
    public function influxUrl(): string
    {
        return "http://{$this->serverIp()}:8086";
    }

    /**
     * Authentication token for InfluxDB API access.
     */
    public function influxToken(): string
    {
        return 'PUT YOUR TOKEN HERE';
    }

    /**
     * The organization name/ID associated with the InfluxDB instance.
     */
    public function influxOrg(): string
    {
        return 'ORG';
    }

    /**
     * The target bucket name for data storage.
     */
    public function influxBucket(): string
    {
        return 'BUCKET';
    }

    /**
     * Raw hostname or IP address of the InfluxDB server.
     */
    public function influxHost(): string
    {
        return $this->serverIp();
    }

    // =========================================================================
    // MYSQL CONFIGURATION
    // =========================================================================

    /**
     * Data Source Name (DSN) for the PDO MySQL connection.
     */
    public function mysqlDsn(): string
    {
        return "mysql:host={$this->serverIp()};dbname=influx;charset=utf8mb4";
    }

    /**
     * Database user for the MySQL connection.
     */
    public function mysqlUser(): string
    {
        return 'user';
    }

    /**
     * Database password for the MySQL connection.
     */
    public function mysqlPassword(): string
    {
        return 'password';
    }

    // =========================================================================
    // RUNTIME & PERFORMANCE SETTINGS
    // =========================================================================

    /**
     * Number of records to process in a single transaction/batch.
     */
    public function batchSize(): int
    {
        return 5000;
    }

    /**
     * Number of times to attempt a failed operation before stopping.
     */
    public function retryLimit(): int
    {
        return 3;
    }

    /**
     * Maximum number of processing cycles the script should run.
     */
    public function maxCycles(): int
    {
        return 1_000_000;
    }
}