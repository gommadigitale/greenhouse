<?php
// includes/helpers.php

function humanize(string $slug): string {
    return ucwords(str_replace('_', ' ', $slug));
}

function stdDev(array $arr): float {
    $mean = array_sum($arr) / count($arr);
    $sum = 0;
    foreach ($arr as $v) $sum += ($v - $mean) ** 2;
    return round(sqrt($sum / count($arr)), 2);
}

function trendIcon($diff): string {
    if ($diff > 0.1) return '<span title="in aumento">↑</span>';
    if ($diff < -0.1) return '<span title="in diminuzione">↓</span>';
    return '<span title="stabile">→</span>';
}
