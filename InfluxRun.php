<?php
require_once __DIR__ . '/Config/AppConfig.php';
require_once __DIR__ . '/Logger/DebugLogger.php';

require_once __DIR__ . '/Influx/InfluxClient.php';
require_once __DIR__ . '/Influx/InfluxResponseParser.php';
require_once __DIR__ . '/Influx/InfluxServiceManager.php';

require_once __DIR__ . '/Mysql/MysqlConnection.php';
require_once __DIR__ . '/Mysql/PfsenseLogRepository.php';
require_once __DIR__ . '/Mysql/PortCountRepository.php';

require_once __DIR__ . '/Parser/PfsenseMessageParser.php';

require_once __DIR__ . '/Migration/PfsenseSyslogMigrator.php';
require_once __DIR__ . '/Runner/MigrationRunner.php';

use App\Config\AppConfig;
use App\Logger\DebugLogger;
use App\Influx\InfluxClient;
use App\Mysql\MysqlConnection;
use App\Mysql\PfsenseLogRepository;
use App\Mysql\PortCountRepository;
use App\Parser\PfsenseMessageParser;
use App\Migration\PfsenseSyslogMigrator;
use App\Runner\MigrationRunner;

$config = new AppConfig();
$logger = new DebugLogger();

$pdo = MysqlConnection::create($config);

$migrator = new PfsenseSyslogMigrator(
    $config,
    new InfluxClient($config, $logger),
    new PfsenseMessageParser(),
    new PfsenseLogRepository($pdo),
    new PortCountRepository($pdo),
    $logger
);

$runner = new MigrationRunner($migrator, $logger);
$runner->run();
