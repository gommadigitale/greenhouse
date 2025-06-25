<?php 
function renderSensorCard(string $id, string $name, array $data, ?array $targetRange, ?string $selectedPlant): string {
    if (empty($data)) return '';

    $last = end($data);
    $length = count($data);
    $prev_index = max(0, $length - 3);
    $prev = $data[$prev_index];

    $temp_diff = $last['temperature'] - $prev['temperature'];
    $hum_diff = $last['humidity'] - $prev['humidity'];
    $temp_trend = trendIcon($temp_diff);
    $hum_trend = trendIcon($hum_diff);

    ob_start();
    ?>
    <div class="card">
        <h2><?= htmlspecialchars($name) ?> - 🌡️<?= round($last['temperature'], 1) ?>C <?= $temp_trend ?> 💦<?= floor($last['humidity']) ?>% <?= $hum_trend ?> <small>(🔋<?= $last['battery'] ?>%)</small></h2>
        <canvas id="<?= $id ?>-chart"></canvas>
        <p id="<?= $id ?>-stats"></p>
        <button onclick="resetZoom('<?= $id ?>')">Reset Zoom</button>
        <button onclick="exportChart('<?= $id ?>')">📸 Esporta PNG</button>

        <?php if ($targetRange): 
            $stats = calcStats($data, $targetRange, $selectedPlant);
            if (!empty($stats)): ?>
                <div class='stat-box'>
                    <h3>📊 Statistiche Dettagliate</h3>
                    <?php if ($targetRange['zone'] === ($id === 'sensor1' ? 1 : 2)): ?>
                        <h4>🪴 <?= humanize($stats['name']) ?> è in questa zona</h4>
                    <?php endif; ?>
                    <ul>
                        <li>Deviazione standard temperatura: <?= $stats['stdTemp'] ?>°C</li>
                        <li>Deviazione standard umidità: <?= $stats['stdHumi'] ?>%</li>
                        <li>% tempo fuori range temperatura: <?= $stats['tempOutPct'] ?>%</li>
                        <li>% tempo fuori range umidità: <?= $stats['humiOutPct'] ?>%</li>
                        <li>Durata totale fuori range stimata: <?= $stats['durationOut'] ?> ore</li>
                    </ul>
                    <div class='legend-box'>
                        La <strong>deviazione standard</strong> misura quanto i valori variano rispetto alla media.<br>
                        Le <strong>percentuali fuori range</strong> indicano quanto spesso temperatura e umidità si sono allontanate dai valori ideali della pianta selezionata.<br>
                        La <strong>durata fuori range</strong> è una stima del tempo totale (in ore) in cui le condizioni non sono state ottimali.
                    </div>
                    <div style='margin-top:16px'>🌡️ <?= $stats['lastTempIssue'] ?></div>
                    <div>💦 <?= $stats['lastHumiIssue'] ?></div>
                </div>
            <?php endif;
        endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function renderPlantAnalysis(array $sensorData, array $plantRanges): string {
    $lastData = [];

    foreach (SENSORS as $id => $name) {
        $zone = ($id === 'sensor1') ? 1 : 2;
        if (!empty($sensorData[$id])) {
            $last = end($sensorData[$id]);
            $lastData[$zone] = [
                'temp' => $last['temperature'],
                'humi' => $last['humidity']
            ];
        }
    }

    $problems = $okPlants = [];

    foreach ($plantRanges as $plant => $range) {
        $zone = $range['zone'] ?? 1;
        if (!isset($lastData[$zone])) continue;

        $temp = $lastData[$zone]['temp'];
        $humi = $lastData[$zone]['humi'];
        $issue = [];

        if ($temp < $range['tempMin']) $issue[] = "Temperatura troppo bassa (min ideale {$range['tempMin']}°C)";
        if ($temp > $range['tempMax']) $issue[] = "Temperatura troppo alta (max ideale {$range['tempMax']}°C)";
        if ($humi < $range['humMin']) $issue[] = "Umidità troppo bassa (min ideale {$range['humMin']}%)";
        if ($humi > $range['humMax']) $issue[] = "Umidità troppo alta (max ideale {$range['humMax']}%)";

        if ($issue) {
            $problems[] = [
                'name' => humanize($plant) . " (Zona $zone)",
                'issues' => $issue
            ];
        } else {
            $okPlants[] = humanize($plant) . " (Zona $zone)";
        }
    }

    ob_start();
    ?>
    <div class="card">
        <h2>🌡️ Analisi Attuale: Stato delle Piante</h2>
        <?php if (empty($problems)): ?>
            <p>✅ Tutte le piante sono attualmente entro i range ideali! 👍</p>
        <?php else: ?>
            <h3>⚠️ Piante fuori range:</h3>
            <ul>
                <?php foreach ($problems as $p): ?>
                    <li>
                        <strong><?= htmlspecialchars($p['name']) ?></strong>
                        <ul>
                            <?php foreach ($p['issues'] as $i): ?>
                                <li><?= htmlspecialchars($i) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($okPlants)): ?>
            <h3>✅ Piante entro i range:</h3>
            <ul>
                <?php foreach ($okPlants as $ok): ?>
                    <li><?= htmlspecialchars($ok) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function renderPlantRanking(array $sensorData, array $plantRanges, ?string $selectedPlant = null): string {
    $scores = [];

    foreach ($plantRanges as $plant => $range) {
        $zone = $range['zone'] ?? 1;
        $sensorId = $zone === 1 ? 'sensor1' : 'sensor2';

        if (empty($sensorData[$sensorId])) continue;

        $stats = calcStats($sensorData[$sensorId], $range, $plant);
        if (!$stats) continue;

        $score = 100
            - ($stats['tempOutPct'] * 0.5)
            - ($stats['humiOutPct'] * 0.5)
            - ($stats['stdTemp'] * 2)
            - ($stats['stdHumi'] * 1.5);

        $scores[] = [
            'plant' => humanize($plant),
            'zone' => $zone,
            'score' => max(0, round($score, 2)),
            'stats' => $stats
        ];
    }

    usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

    // Prepara array JS-friendly
    $labels = [];
    $values = [];
    foreach ($scores as $s) {
        $labels[] = $s['plant'] . " (Z{$s['zone']})";
        $values[] = $s['score'];
    }

    // Output HTML + JS inline
    ob_start(); ?>
    <div class="card">
        <h2>🏆 Classifica Salute Piante</h2>
        <canvas id="plant-ranking-chart" height="250"></canvas>
        <table class="ranking-table responsive-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pianta</th>
                    <th>Zona</th>
                    <th>Punteggio</th>
                    <th>Temp out %</th>
                    <th>Hum out %</th>
                    <th>Std Dev Temp</th>
                    <th>Std Dev Hum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scores as $i => $s): ?>
                    <tr>
                        <td data-label="#"><?= $i + 1 ?></td>
                        <td data-label="Pianta"><?= htmlspecialchars($s['plant']) ?></td>
                        <td data-label="Zona"><?= $s['zone'] ?></td>
                        <td data-label="Punteggio"><strong><?= $s['score'] ?></strong></td>
                        <td data-label="Temp out %"><?= $s['stats']['tempOutPct'] ?>%</td>
                        <td data-label="Hum out %"><?= $s['stats']['humiOutPct'] ?>%</td>
                        <td data-label="Std Dev Temp"><?= $s['stats']['stdTemp'] ?></td>
                        <td data-label="Std Dev Hum"><?= $s['stats']['stdHumi'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
    const rankingLabels = <?= json_encode($labels) ?>;
    const rankingScores = <?= json_encode($values) ?>;

    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('plant-ranking-chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: rankingLabels,
                datasets: [{
                    label: 'Punteggio Salute',
                    data: rankingScores,
                    backgroundColor: rankingScores.map(score => {
                        if (score >= 80) return 'rgba(34,197,94,0.7)';     // verde
                        if (score >= 60) return 'rgba(250,204,21,0.7)';    // giallo
                        if (score >= 40) return 'rgba(251,191,36,0.7)';    // arancione
                        return 'rgba(239,68,68,0.7)';                      // rosso
                    }),
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { color: '#ccc' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    x: {
                        ticks: { color: '#ccc' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: '#ccc' }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `Salute: ${ctx.parsed.y}`
                        }
                    }
                }
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

function renderPlantCard($plantData) {
    ob_start();
    ?>
    <div class="card">
        <h2><?= htmlspecialchars($plantData['name']) ?></h2>
        <?php if (!empty($plantData['image'])): ?>
            <!-- <img src="<?= htmlspecialchars($plantData['image']) ?>" alt="<?= htmlspecialchars($plantData['name']) ?>" style="max-width: 300px;"> -->
        <?php endif; ?>
        <ul>
            <li><strong>Origine:</strong> <?= $plantData['origin'] ?></li>
            <li><strong>Temperatura ideale:</strong> <?= $plantData['tempMin'] ?> °C – <?= $plantData['tempMax'] ?> °C</li>
            <li><strong>Umidità ideale:</strong> <?= $plantData['humMin'] ?>% – <?= $plantData['humMax'] ?>%</li>
            <li><strong>Luce:</strong> <?= $plantData['light'] ?></li>
            <li><strong>Substrato:</strong> <?= $plantData['substrate'] ?></li>
            <li><strong>Note:</strong> <?= $plantData['notes'] ?></li>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}

function renderDailyStats(array $sensorData): string {
    $dailyStats = [];

    foreach ($sensorData as $sensorId => $readings) {
        foreach ($readings as $entry) {
            $dt = new DateTime($entry['timestamp']);
            $hour = (int)$dt->format('H');
            $dateKey = $dt->format('Y-m-d');
            $isNight = ($hour < 6) || ($hour >= 20);
            if ($hour < 6) {
                $dateKey = (new DateTime($entry['timestamp']))->modify('-1 day')->format('Y-m-d');
            }
            $period = $isNight ? 'night' : 'day';
            $temp = $entry['temperature'];
            $hum = $entry['humidity'];

            if (!isset($dailyStats[$sensorId][$dateKey][$period])) {
                $dailyStats[$sensorId][$dateKey][$period] = [
                    'temp_min' => $temp,
                    'temp_max' => $temp,
                    'temp_sum' => $temp,
                    'hum_min' => $hum,
                    'hum_max' => $hum,
                    'hum_sum' => $hum,
                    'count' => 1
                ];
            } else {
                $stats = &$dailyStats[$sensorId][$dateKey][$period];
                $stats['temp_min'] = min($stats['temp_min'], $temp);
                $stats['temp_max'] = max($stats['temp_max'], $temp);
                $stats['hum_min'] = min($stats['hum_min'], $hum);
                $stats['hum_max'] = max($stats['hum_max'], $hum);
                $stats['temp_sum'] += $temp;
                $stats['hum_sum'] += $hum;
                $stats['count']++;
            }
        }
    }

    ob_start(); ?>
    <div class="card"> 
    <h2>📊 Statistiche Giorno/Notte per Temperatura e Umidità</h2>
    <?php foreach ($dailyStats as $sensorId => $days): 
        $zone = ($sensorId === 'sensor1') ? 1 : 2;
    ?>
        <h3>Zona <?= $zone ?></h3>
        <table border="1" cellpadding="6" cellspacing="0" class="responsive-table">
            <thead>
                <tr>
                    <th rowspan="2">Data</th>
                    <th colspan="3">🌞 Giorno - Temperatura (°C)</th>
                    <th colspan="3">🌞 Giorno - Umidità (%)</th>
                    <th colspan="3">🌙 Notte - Temperatura (°C)</th>
                    <th colspan="3">🌙 Notte - Umidità (%)</th>
                </tr>
                <tr>
                    <th>Min</th><th>Media</th><th>Max</th>
                    <th>Min</th><th>Media</th><th>Max</th>
                    <th>Min</th><th>Media</th><th>Max</th>
                    <th>Min</th><th>Media</th><th>Max</th>
                </tr>
            </thead>
            <tbody>
                <?php ksort($days); foreach ($days as $date => $parts): ?>
                    <tr>
                        <td data-label="Data"><?= htmlspecialchars($date) ?></td>
                        <?php
                        foreach (['day', 'night'] as $period) {
                            $emoji = ($period === 'day') ? '🌞' : '🌙';
                            if (isset($parts[$period])) {
                                $s = $parts[$period];
                                $tAvg = round($s['temp_sum'] / $s['count'], 2);
                                $hAvg = round($s['hum_sum'] / $s['count'], 2);
                        ?>
                            <td class="min" data-label="<?=$emoji?> Temperatura Min"><?=$s['temp_min']?>°C</td>
                            <td class="avg" data-label="<?=$emoji?> Temperatura Media"><?=$tAvg?>°C</td>
                            <td class="max" data-label="<?=$emoji?> Temperatura Max"><?=$s['temp_max']?>°C</td>
                            <td class="min" data-label="<?=$emoji?> Umidità Min"><?=$s['hum_min']?>%</td>
                            <td class="avg" data-label="<?=$emoji?> Umidità Media"><?=$hAvg?>%</td>
                            <td class="max" data-label="<?=$emoji?> Umidità Max"><?=$s['hum_max']?>%</td>
                        <?php
                            } else {
                                echo str_repeat('<td>—</td>', 6);
                            }
                        }
                        ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}


function renderMonthStats(array $sensorData): string {
    $monthlyStats = [];

    foreach ($sensorData as $sensorId => $readings) {
        foreach ($readings as $entry) {
            $dt = new DateTime($entry['timestamp']);
            $hour = (int)$dt->format('H');
            $monthKey = $dt->format('Y-m');
            $isNight = ($hour < 6) || ($hour >= 20);
            if ($hour < 6) {
                $monthKey = (new DateTime($entry['timestamp']))->modify('-1 day')->format('Y-m');
            }

            $period = $isNight ? 'night' : 'day';
            $temp = $entry['temperature'];
            $hum = $entry['humidity'];

            if (!isset($monthlyStats[$sensorId][$monthKey][$period])) {
                $monthlyStats[$sensorId][$monthKey][$period] = [
                    'temp_min' => $temp,
                    'temp_max' => $temp,
                    'temp_sum' => $temp,
                    'hum_min' => $hum,
                    'hum_max' => $hum,
                    'hum_sum' => $hum,
                    'count' => 1
                ];
            } else {
                $s = &$monthlyStats[$sensorId][$monthKey][$period];
                $s['temp_min'] = min($s['temp_min'], $temp);
                $s['temp_max'] = max($s['temp_max'], $temp);
                $s['temp_sum'] += $temp;
                $s['hum_min'] = min($s['hum_min'], $hum);
                $s['hum_max'] = max($s['hum_max'], $hum);
                $s['hum_sum'] += $hum;
                $s['count']++;
            }
        }
    }

    ob_start(); ?>
    <div class="card">
        <h2>📈 Statistiche Mensili Giorno/Notte per Temperatura e Umidità</h2>
        <?php foreach ($monthlyStats as $sensorId => $months):
            $zone = ($sensorId === 'sensor1') ? 1 : 2;
        ?>
        <h3>Zona <?= $zone ?></h3>
        <table border="1" cellpadding="6" cellspacing="0" class="responsive-table">
            <thead>
                <tr>
                    <th rowspan="2">Mese</th>
                    <th colspan="3">🌞 Giorno - Temperatura (°C)</th>
                    <th colspan="3">🌞 Giorno - Umidità (%)</th>
                    <th colspan="3">🌙 Notte - Temperatura (°C)</th>
                    <th colspan="3">🌙 Notte - Umidità (%)</th>
                </tr>
                <tr>
                    <th>Min</th><th>Media</th><th>Max</th>
                    <th>Min</th><th>Media</th><th>Max</th>
                    <th>Min</th><th>Media</th><th>Max</th>
                    <th>Min</th><th>Media</th><th>Max</th>
                </tr>
            </thead>
            <tbody>
                <?php ksort($months); foreach ($months as $month => $parts): ?>
                    <tr>
                        <td><?= htmlspecialchars($month) ?></td>
                        <?php foreach (['day', 'night'] as $period): ?>
                            <?php if (isset($parts[$period])):
                                $emoji = ($period === 'day') ? '🌞' : '🌙';
                                $s = $parts[$period];
                                $avgTemp = $s['temp_sum'] / $s['count'];
                                $avgHum = $s['hum_sum'] / $s['count'];
                            ?>
                                <td class="min" data-label="<?=$emoji?> Temperatura Min"><?= number_format($s['temp_min'], 1) ?>°C</td>
                                <td class="avg" data-label="<?=$emoji?> Temperatura Media"><?= number_format($avgTemp, 1) ?>°C</td>
                                <td class="max" data-label="<?=$emoji?> Temperatura Max"><?= number_format($s['temp_max'], 1) ?>°C</td>
                                <td class="min" data-label="<?=$emoji?> Umidità Min"><?= number_format($s['hum_min'], 0) ?>%</td>
                                <td class="avg" data-label="<?=$emoji?> Umidità Media"><?= number_format($avgHum, 0) ?>%</td>
                                <td class="max" data-label="<?=$emoji?> Umidità Max"><?= number_format($s['hum_max'], 0) ?>%</td>
                            <?php else: ?>
                                <?= str_repeat('<td>—</td>', 6) ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
