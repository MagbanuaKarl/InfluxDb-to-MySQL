<?php
declare(strict_types=1);

namespace App\Parser;

final class PfsenseMessageParser
{
    public function parse(string $message, string $timestamp): ?array
    {
        $message = trim($message);
        if ($message === '') {
            return null;
        }

        if (
            str_starts_with($message, 'DHCP') ||
            str_starts_with($message, '89') ||
            str_contains($message, ':')
        ) {
            return null;
        }

        $parts = explode(',', $message);
        if (count($parts) < 22) {
            return null;
        }

        if (!str_contains(strtolower($message), 'tcp')) {
            return null;
        }

        $interface = strtolower(trim($parts[4]));
        $status    = strtolower(trim($parts[6]));

        if (!in_array($status, ['pass', 'block'], true)) {
            return null;
        }

        if ($parts[8] !== '4' || $interface === 'lo0') {
            return null;
        }

        return [
            'timestamp'        => $timestamp,
            'protocol'         => 'tcp',
            'direction'        => trim($parts[7]),
            'interface'        => $interface,
            'source_ip'        => trim($parts[18]),
            'destination_ip'   => trim($parts[19]),
            'port_source'      => is_numeric($parts[20]) ? (int)$parts[20] : null,
            'port_destination' => is_numeric($parts[21]) ? (int)$parts[21] : null,
            'status'           => $status,
        ];
    }
}
