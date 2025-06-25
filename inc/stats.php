<?php
// includes/stats.php
require_once __DIR__ . '/helpers.php';

function calcStats(array $data, array $range, string $selectedPlant): array {
    $count = count($data);
    if ($count === 0) return [];

    $tempOut = $humiOut = 0;
    foreach ($data as $row) {
        if ($row['temperature'] < $range['tempMin'] || $row['temperature'] > $range['tempMax']) $tempOut++;
        if ($row['humidity'] < $range['humMin'] || $row['humidity'] > $range['humMax']) $humiOut++;
    }

    $last = end($data);
    $lastTempIssue = '✓ OK';
    $lastHumiIssue = '✓ OK';

    if ($last['temperature'] < $range['tempMin']) $lastTempIssue = "Temperatura troppo bassa (min ideale {$range['tempMin']}°C)";
    elseif ($last['temperature'] > $range['tempMax']) $lastTempIssue = "Temperatura troppo alta (max ideale {$range['tempMax']}°C)";
    if ($last['humidity'] < $range['humMin']) $lastHumiIssue = "Umidità troppo bassa (min ideale {$range['humMin']}%)";
    elseif ($last['humidity'] > $range['humMax']) $lastHumiIssue = "Umidità troppo alta (max ideale {$range['humMax']}%)";

    return [
        'name' => $selectedPlant,
        'total' => $count,
        'tempOut' => $tempOut,
        'humiOut' => $humiOut,
        'tempOutPct' => round(($tempOut / $count) * 100, 2),
        'humiOutPct' => round(($humiOut / $count) * 100, 2),
        'durationOut' => round(($tempOut + $humiOut) * 5 / 60, 2),
        'stdTemp' => stdDev(array_column($data, 'temperature')),
        'stdHumi' => stdDev(array_column($data, 'humidity')),
        'lastTempIssue' => $lastTempIssue,
        'lastHumiIssue' => $lastHumiIssue
    ];
}
