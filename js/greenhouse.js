const charts = {};
let currentDate = new Date();

document.addEventListener('DOMContentLoaded', () => {
    insertCurrentDayTitle();
    initializeCharts();
    zoomToFullRange();
    // autoZoomToLastDay();
});

function insertCurrentDayTitle() {
    const title = document.createElement('h2');
    title.id = 'current-day-title';
    title.style.color = '#fafafa';
    title.style.margin = '1rem 0';
    document.body.insertBefore(title, document.querySelector('form').nextSibling);
}

function initializeCharts() {
    Object.entries(sensorData).forEach(([id, data]) => {
        const ctx = document.getElementById(`${id}-chart`);
        if (!ctx) return;

        const labels = data.map(d => d.timestamp);
        const temp = data.map(d => d.temperature);
        const humi = data.map(d => d.humidity);
        const last = data[data.length - 1];

        updateStatsDisplay(id, last, temp, humi);

        charts[id] = new Chart(ctx, {
            type: 'line',
            data: buildDatasets(labels, temp, humi),
            options: buildChartOptions(temp, humi)
        });
    });
}

function updateStatsDisplay(id, last, temp, humi) {
    const avgTemp = (temp.reduce((a, b) => a + b, 0) / temp.length).toFixed(2);
    const avgHumi = (humi.reduce((a, b) => a + b, 0) / humi.length).toFixed(2);
    const el = document.getElementById(`${id}-stats`);
    if (el) {
        el.innerHTML = `
            <strong>🌡️Temperatura</strong>: Attuale ${last.temperature.toFixed(2)}°C, Media ${avgTemp}°C<br>
            <strong>💦Umidità</strong>: Attuale ${last.humidity.toFixed(2)}%, Media ${avgHumi}%
        `;
    }
}

function buildDatasets(labels, temp, humi) {
    const datasets = [
        {
            label: 'Temperatura (°C)',
            data: temp,
            borderColor: '#f87171',
            backgroundColor: 'rgba(248, 113, 113, 0.1)',
            tension: 0.3,
            yAxisID: 'y'
        },
        {
            label: 'Umidità (%)',
            data: humi,
            borderColor: '#38bdf8',
            backgroundColor: 'rgba(56, 189, 248, 0.1)',
            tension: 0.3,
            yAxisID: 'y1'
        }
    ];

    if (targetRange) {
        datasets.push(
            ...['tempMin', 'tempMax'].map(key => ({
                label: key === 'tempMin' ? 'Temp Min' : 'Temp Max',
                data: Array(labels.length).fill(targetRange[key]),
                borderColor: '#f87171',
                borderDash: [5, 5],
                borderWidth: 1,
                yAxisID: 'y'
            })),
            ...['humMin', 'humMax'].map(key => ({
                label: key === 'humMin' ? 'Hum Min' : 'Hum Max',
                data: Array(labels.length).fill(targetRange[key]),
                borderColor: '#38bdf8',
                borderDash: [5, 5],
                borderWidth: 1,
                yAxisID: 'y1'
            }))
        );
    }

    return {
        labels: labels,
        datasets: datasets
    };
}

function buildChartOptions(temp, humi) {
    return {
        responsive: true,
        maintainAspectRatio: window.innerWidth > 600,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            annotation: {
                annotations: getNightAnnotations()
            },
            zoom: {
                pan: { enabled: true, mode: 'x' },
                zoom: {
                    wheel: { enabled: true, modifierKey: 'ctrl' },
                    pinch: { enabled: true },
                    drag: { enabled: true, backgroundColor: 'rgba(255,255,255,0.1)' },
                    mode: 'x'
                }
            },
            tooltip: { mode: 'index', intersect: false },
            legend: {
                labels: { color: '#e0e0e0' }
            }
        },
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'hour',
                    displayFormats: { hour: 'HH:mm' }
                },
                ticks: { color: '#ccc' },
                grid: { color: 'rgba(255, 255, 255, 0.1)' }
            },
            y: {
                ticks: { color: '#ccc' },
                grid: { color: 'rgba(255, 0, 0, 0.1)' },
                min: Math.min(...temp) - 5,
                max: Math.max(...temp) + 5,
            },
            y1: {
                position: 'right',
                ticks: { color: '#ccc' },
                grid: { color: 'rgba(0, 0, 255, 0.1)' },
                min: Math.min(...humi) - 10,
                max: Math.max(...humi) + 10,
            }
        }
    };
}

function getNightAnnotations() {
    const annotations = {};
    const nightStartHour = 20;
    const nightEndHour = 6;

    let allTimestamps = [];
    Object.values(sensorData).forEach(dataArray => {
        allTimestamps.push(...dataArray.map(d => new Date(d.timestamp)));
    });

    if (allTimestamps.length === 0) return annotations;

    const minDate = new Date(Math.min(...allTimestamps.map(d => d.getTime())));
    const maxDate = new Date(Math.max(...allTimestamps.map(d => d.getTime())));

    minDate.setHours(0, 0, 0, 0);
    maxDate.setHours(23, 59, 59, 999);

    let current = new Date(minDate);
    let i = 0;

    while (current < maxDate) {
        const nightStart = new Date(current);
        nightStart.setHours(nightStartHour, 0, 0, 0);

        const nightEnd = new Date(current);
        nightEnd.setDate(nightEnd.getDate() + 1);
        nightEnd.setHours(nightEndHour, 0, 0, 0);

        annotations[`night-${i}`] = {
            type: 'box',
            xMin: nightStart.toISOString(),
            xMax: nightEnd.toISOString(),
            backgroundColor: 'rgba(100, 100, 100, 0.2)',
            borderWidth: 0
        };

        current.setDate(current.getDate() + 1);
        i++;
    }

    return annotations;
}

function autoZoomToLastDay() {
    const firstSensor = Object.values(sensorData)[0];
    if (firstSensor && firstSensor.length > 0) {
        const lastDate = new Date(firstSensor.slice(-1)[0].timestamp);
        updateChartsToDate(lastDate);
    }
}
function zoomToFullRange() {
    let allTimestamps = [];
    Object.values(sensorData).forEach(dataArray => {
        allTimestamps.push(...dataArray.map(d => new Date(d.timestamp)));
    });

    if (allTimestamps.length === 0) return;

    const start = new Date(Math.min(...allTimestamps.map(d => d.getTime())));
    const end = new Date(Math.max(...allTimestamps.map(d => d.getTime())));

    Object.values(charts).forEach(chart => {
        chart.options.scales.x.min = start;
        chart.options.scales.x.max = end;

        // aggiorna anche le bande notte
        chart.options.plugins.annotation.annotations = getNightAnnotations();
        chart.update();
    });

    currentDate = new Date(end); // aggiorna currentDate per navigazione successiva

    const title = document.getElementById('current-day-title');
    if (title) {
        title.textContent = "Intervallo completo";
    }
}

function updateChartsToDate(date) {
    currentDate = new Date(date);
    currentDate.setHours(0, 0, 0, 0);

    const start = new Date(currentDate);
    const end = new Date(currentDate);
    end.setHours(23, 59, 59, 999);

    Object.values(charts).forEach(chart => {
        chart.options.scales.x.min = start;
        chart.options.scales.x.max = end;

        // Aggiorna anche le bande notte
        chart.options.plugins.annotation.annotations = getNightAnnotations();

        chart.update();
    });

    const title = document.getElementById('current-day-title');
    if (title) {
        title.textContent = currentDate.toLocaleDateString('it-IT', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
}

function navigateDays(days) {
    const newDate = new Date(currentDate);
    newDate.setDate(newDate.getDate() + days);
    updateChartsToDate(newDate);
}

function exportChart(id) {
    const link = document.createElement('a');
    link.download = `${id}_chart.png`;
    link.href = charts[id].toBase64Image();
    link.click();
}

function resetZoom(id) {
    charts[id]?.resetZoom();
}

function setRange(type) {
    const now = new Date();
    let from;
    if (type === '24h') from = new Date(now.getTime() - 24 * 60 * 60 * 1000);
    else if (type === '7d') from = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

    document.querySelector('[name=from]').value = from.toISOString().slice(0, 16);
    document.querySelector('[name=to]').value = now.toISOString().slice(0, 16);
    document.querySelector('form').submit();
}
