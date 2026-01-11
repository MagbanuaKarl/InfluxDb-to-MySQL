<?php
declare(strict_types=1);

namespace App\Mysql;

use PDO;

final class PortCountRepository
{
    private array $portTables = [
        445  => '445_port_count',
        5353 => '5353_port_count',
        5355 => '5355_port_count',
    ];

    public function __construct(private PDO $pdo) {}

    public function update(array $portCounts): void
    {
        foreach ($this->portTables as $port => $table) {
            if (empty($portCounts[$port])) {
                continue;
            }

            $sql = "
                INSERT INTO {$table} (date, port, total_count)
                VALUES (:date, :port, :count)
                ON DUPLICATE KEY UPDATE
                    total_count = {$table}.total_count + VALUES(total_count),
                    updatedatetime = NOW()
            ";

            $stmt = $this->pdo->prepare($sql);

            foreach ($portCounts[$port] as $date => $count) {
                $stmt->execute([
                    ':date'  => $date,
                    ':port'  => (string)$port,
                    ':count' => $count
                ]);
            }
        }
    }
}
