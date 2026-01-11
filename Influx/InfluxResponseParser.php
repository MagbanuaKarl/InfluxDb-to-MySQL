<?php
declare(strict_types=1);

namespace App\Influx;

final class InfluxResponseParser
{
    public static function parse(string $response): array
    {
        $lines = explode("\n", trim($response));
        $headers = null;
        $data = [];

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            $row = str_getcsv($line, ',', '"', '\\');

            if ($headers === null) {
                $headers = $row;
                continue;
            }

            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        return $data;
    }
}
