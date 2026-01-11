<?php
declare(strict_types=1);

namespace App\Mysql;

use App\Config\AppConfig;
use PDO;
use PDOException;

final class MysqlConnection
{
    public static function create(AppConfig $config): PDO
    {
        return new PDO(
            $config->mysqlDsn(),
            $config->mysqlUser(),
            $config->mysqlPassword(),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
}
