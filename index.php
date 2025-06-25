<?php
    require_once __DIR__ . '/inc/config.php';
    require_once __DIR__ . '/inc/helpers.php';
    require_once __DIR__ . '/inc/sensors.php';
    require_once __DIR__ . '/inc/stats.php';
    require_once __DIR__ . '/inc/components.php';
    require_once __DIR__ . '/inc/plants.php';

    $from = $_GET['from'] ?? null;
    $to = $_GET['to'] ?? null;
    $selectedPlant = $_GET['plant'] ?? null;

    $sensorData = getSensorData($from, $to);
    $targetRange = $plantRanges[$selectedPlant] ?? null;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🪴 Dettagli Serra</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<h1>🪴 Analisi Avanzata Serra (<small>Ultimo aggiornamento: <?= date('d/m/Y H:i') ?></small>)</h1>
<form method="get">
    <div class="w-100">
        <div class="flex gap">
            <label>Da: <input type="datetime-local" name="from" value="<?= htmlspecialchars($from ?? "") ?>"></label>
            <label>A: <input type="datetime-local" name="to" value="<?= htmlspecialchars($to ?? "") ?>"></label>
            <label title="controlla la temperatura ed umidità massima e minima per ogni pianta nel grafico">Verifica Pianta:
                <select name="plant" onchange="this.form.submit()">
                    <option value="">- Nessuno -</option>
                    <?php
                    uksort($plantRanges, fn($a, $b) => strcmp(humanize($a), humanize($b)));
                    foreach ($plantRanges as $key => $range) {
                        $label = humanize($key)." ({$range['tempMin']}C-{$range['tempMax']}C {$range['humMin']}%-{$range['humMax']}%)";
                        $selected = ($selectedPlant === $key) ? 'selected' : '';
                        echo "<option value=\"$key\" $selected>$label</option>";
                    }
                    ?>
                </select>
            </label>
        </div>
        <div class="mt-1">
            <button type="button" onclick="setRange('24h')">Ultime 24h</button>
            <button type="button" onclick="setRange('7d')">Ultimi 7 giorni</button>
            <button type="submit">Filtra</button>
        </div>
    </div>
    <div class="flex gap m-row">
        <button type="button" onclick="navigateDays(-1)">← Giorno Precedente</button>
        <button type="button" onclick="updateChartsToDate(new Date())">Oggi</button>
        <button type="button" onclick="navigateDays(1)">Giorno Successivo →</button>
    </div>
    <div class="flex gap m-row">
        <button type="button" onclick="zoomToFullRange()">🔍 Vista completa</button>
    </div>
</form>

<?php
if ($selectedPlant && isset($plantRanges[$selectedPlant])) {
    echo renderPlantCard($plantRanges[$selectedPlant]);
}
foreach (SENSORS as $id => $name) {
    echo renderSensorCard($id, $name, $sensorData[$id], $targetRange, $selectedPlant);
}
echo renderDailyStats($sensorData);
echo renderMonthStats($sensorData);
echo renderPlantAnalysis($sensorData, $plantRanges);
echo renderPlantRanking($sensorData, $plantRanges, $selectedPlant);
?>

<script>
    const sensorData = <?= json_encode($sensorData) ?>;
    const targetRange = <?= json_encode($targetRange) ?>;
</script>
<script src="./js/greenhouse.js"></script>

</body>
</html>