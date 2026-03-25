<?php
/**
 * Doji Funding — SVG Icon Helpers
 *
 * Inline SVG icons — no CDN dependency, guaranteed rendering.
 * Based on Lucide icon set (https://lucide.dev).
 */

/**
 * Get SVG path data for each icon.
 * Returns [paths, stroke-color] for each icon name.
 */
function getIconSvgMap(): array {
    $g = '#10B981'; // green
    $o = '#ff9f1a'; // orange
    $b = '#4a9eff'; // blue
    $r = '#ff3b3b'; // red

    return [
        'check' => [
            'paths' => '<polyline points="20 6 9 17 4 12"/>',
            'color' => $g,
        ],
        'check-circle' => [
            'paths' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            'color' => $g,
        ],
        'x-circle' => [
            'paths' => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
            'color' => $r,
        ],
        'target' => [
            'paths' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
            'color' => $g,
        ],
        'chart' => [
            'paths' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
            'color' => $g,
        ],
        'trending' => [
            'paths' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
            'color' => $g,
        ],
        'calendar' => [
            'paths' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
            'color' => $g,
        ],
        'bot' => [
            'paths' => '<rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/>',
            'color' => $g,
        ],
        'coins' => [
            'paths' => '<circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="M16.71 13.88l.7.71-2.82 2.82"/>',
            'color' => $g,
        ],
        'message' => [
            'paths' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
            'color' => $g,
        ],
        'diamond' => [
            'paths' => '<path d="M2.7 10.3a2.41 2.41 0 0 0 0 3.41l7.59 7.59a2.41 2.41 0 0 0 3.41 0l7.59-7.59a2.41 2.41 0 0 0 0-3.41l-7.59-7.59a2.41 2.41 0 0 0-3.41 0Z"/>',
            'color' => $g,
        ],
        'zap' => [
            'paths' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
            'color' => $o,
        ],
        'crown' => [
            'paths' => '<path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/>',
            'color' => $o,
        ],
        'circle-green' => [
            'paths' => '<circle cx="12" cy="12" r="10"/>',
            'color' => $g,
        ],
        'info' => [
            'paths' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
            'color' => $b,
        ],
        'trophy' => [
            'paths' => '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>',
            'color' => $g,
        ],
        'sliders' => [
            'paths' => '<line x1="21" y1="4" x2="14" y2="4"/><line x1="10" y1="4" x2="3" y2="4"/><line x1="21" y1="12" x2="12" y2="12"/><line x1="8" y1="12" x2="3" y2="12"/><line x1="21" y1="20" x2="16" y2="20"/><line x1="12" y1="20" x2="3" y2="20"/><line x1="14" y1="2" x2="14" y2="6"/><line x1="8" y1="10" x2="8" y2="14"/><line x1="16" y1="18" x2="16" y2="22"/>',
            'color' => $g,
        ],
        'shield' => [
            'paths' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
            'color' => $g,
        ],
        'eye' => [
            'paths' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
            'color' => $g,
        ],
        'globe' => [
            'paths' => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
            'color' => $g,
        ],
        'monitor' => [
            'paths' => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
            'color' => $g,
        ],
        'video' => [
            'paths' => '<rect x="2" y="4" width="15" height="13" rx="2"/><polygon points="22 4 17 8.5 17 12.5 22 17 22 4"/>',
            'color' => $g,
        ],
        'pen' => [
            'paths' => '<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>',
            'color' => $g,
        ],
        'users' => [
            'paths' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'color' => $g,
        ],
    ];
}

/**
 * Render an inline SVG icon.
 *
 * @param string $name   Icon name (check, chart, zap, etc.)
 * @param int    $size   Icon size in pixels (default: 18)
 * @param string $class  Additional CSS class(es)
 * @return string HTML for the SVG icon
 */
function icon(string $name, int $size = 18, string $class = ''): string {
    $map = getIconSvgMap();

    if (!isset($map[$name])) {
        return '';
    }

    $icon = $map[$name];
    $color = $icon['color'];
    $paths = $icon['paths'];
    $cls = 'doji-icon' . ($class ? " {$class}" : '');

    return '<span class="' . $cls . '" style="display:inline-flex;align-items:center;justify-content:center;vertical-align:-3px;flex-shrink:0">'
         . '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
         . $paths
         . '</svg>'
         . '</span>';
}

/**
 * Legacy compatibility — no-op since we no longer use CDN scripts.
 */
function iconScripts(?array $only = null): string {
    return '<!-- Icons: inline SVG (no CDN needed) -->';
}

/**
 * Legacy compatibility.
 */
function getIconImports(): array {
    return [];
}

function getIconMap(): array {
    $svgMap = getIconSvgMap();
    $result = [];
    foreach ($svgMap as $name => $data) {
        $result[$name] = [$name, $data['color'], $data['color']];
    }
    return $result;
}
