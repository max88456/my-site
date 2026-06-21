<?php
function chartJson(array $value): string { return htmlspecialchars(json_encode(array_values($value), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); }
function chartLabels(array $rows, string $key): string {
    return chartJson(array_map(function($r) use ($key) {
        return (string)($r[$key] ?? '');
    }, $rows));
}
function chartValues(array $rows, string $key): string {
    return chartJson(array_map(function($r) use ($key) {
        return (float)($r[$key] ?? 0);
    }, $rows));
}
function kpiCard(string $icon, string $label, $value, string $trend = 'активно'): string {
    return '<div class="kpi-card"><div class="kpi-icon">'.htmlspecialchars($icon).'</div><div class="kpi-value">'.htmlspecialchars((string)$value).'</div><div class="kpi-label">'.htmlspecialchars($label).'</div><span class="kpi-trend">↗ '.htmlspecialchars($trend).'</span></div>';
}
?>
