<?php
declare(strict_types=1);

namespace App\Mysql;

use PDO;
use PDOException;

final class PfsenseLogRepository
{
    public function __construct(private PDO $pdo) {}

    public function insertBatch(array $records): int
    {
        if (empty($records)) {
            return 0;
        }

        $placeholders = [];
        $values = [];

        foreach ($records as $r) {
            $placeholders[] = '(?,?,?,?,?,?,?,?,?)';

            $values[] = $r['timestamp'];
            $values[] = $r['protocol'];
            $values[] = $r['direction'];
            $values[] = $r['interface'];
            $values[] = $r['source_ip'];
            $values[] = $r['destination_ip'];
            $values[] = $r['port_source'];
            $values[] = $r['port_destination'];
            $values[] = $r['status'];
        }

        $sql = "
            INSERT INTO pfsense_logs
            (timestamp, protocol, direction, interface, source_ip, destination_ip, port_source, port_destination, status)
            VALUES " . implode(',', $placeholders);

        $this->pdo->beginTransaction();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        $this->pdo->commit();

        return count($records);
    }
}
