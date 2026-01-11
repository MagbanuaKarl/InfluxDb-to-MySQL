<?php
declare(strict_types=1);

namespace App\Runner;

use App\Migration\PfsenseSyslogMigrator;
use App\Logger\DebugLogger;

final class MigrationRunner
{
    public function __construct(
        private PfsenseSyslogMigrator $migrator,
        private DebugLogger $logger
    ) {}

    public function run(): void
    {
        echo "🚀 Starting PFSENSE SYSLOG migration\n";

        try {
            if ($this->isRestrictedTime()) {
                echo "🛑 Restricted time window. Exiting.\n";
                return;
            }

            $success = $this->migrator->migrate();

            echo $success
                ? "✅ Migration successful\n"
                : "⚠️ Migration completed with no data\n";

        } catch (\Throwable $e) {
            $this->logger->logError('MigrationRunner::run', $e);
        }
    }

    private function isRestrictedTime(): bool
    {
        $now   = new \DateTime();
        $start = new \DateTime('today 00:00');
        $end   = new \DateTime('today 00:10');

        return ($now >= $start && $now < $end);
    }
}
