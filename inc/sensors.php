<?php
// includes/sensors.php
require_once __DIR__ . '/config.php';

function getSensorData($from = null, $to = null): array {
    $results = [];
    foreach (SENSORS as $id => $name) {
        $file = DATA_DIR . $id . '.csv';
        $results[$id] = file_exists($file) ? processCsvFile($file, $from, $to) : [];
    }
    return $results;
}

function processCsvFile(string $file, $from = null, $to = null): array {
    $data = [];
    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // Skip header
        while (($row = fgetcsv($handle)) !== false) {
            $utcDate = DateTime::createFromFormat('Y-m-d H:i:s', $row[0], new DateTimeZone('UTC'));
            $utcDate->setTimezone(new DateTimeZone('Europe/Rome'));
            $dateStr = $utcDate->format('Y-m-d\TH:i');

            if ($from && $dateStr < $from) continue;
            if ($to && $dateStr > $to) continue;

            $data[] = [
                'timestamp' => $dateStr,
                'temperature' => (float)$row[1],
                'humidity' => (float)$row[2],
                'battery' => (int)$row[3]
            ];
        }
        fclose($handle);
    }
    return $data;
}
