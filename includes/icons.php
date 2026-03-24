<?php
/**
 * Doji Funding — Animated Icon Helpers
 *
 * Uses @animated-color-icons/lucide-wc web components.
 * Icons animate on hover via the .al-icon-wrapper class.
 * CDN: https://cdn.jsdelivr.net/npm/@animated-color-icons/lucide-wc/
 */

// ─── Icon name → Web Component mapping ───
// Maps our short names to [lucide-component-name, primary-color, secondary-color]
function getIconMap(): array {
    return [
        'check'        => ['check',               '#10B981', '#0d9488'],
        'check-circle' => ['circle-check',         '#10B981', '#0d9488'],
        'x-circle'     => ['circle-x',             '#ff3b3b', '#dc2626'],
        'target'       => ['target',               '#10B981', '#0d9488'],
        'chart'        => ['bar-chart-3',           '#10B981', '#0d9488'],
        'trending'     => ['trending-up',           '#10B981', '#0d9488'],
        'calendar'     => ['calendar',             '#10B981', '#0d9488'],
        'bot'          => ['bot',                  '#10B981', '#0d9488'],
        'coins'        => ['coins',                '#10B981', '#0d9488'],
        'message'      => ['message-square',        '#10B981', '#0d9488'],
        'diamond'      => ['diamond',              '#10B981', '#0d9488'],
        'zap'          => ['zap',                  '#ff9f1a', '#d97706'],
        'crown'        => ['crown',                '#ff9f1a', '#d97706'],
        'circle-green' => ['circle',               '#10B981', '#0d9488'],
        'info'         => ['info',                 '#4a9eff', '#2563eb'],
        // Missing icons now available
        'trophy'       => ['trophy',               '#10B981', '#0d9488'],
        'sliders'      => ['sliders-horizontal',    '#10B981', '#0d9488'],
        'shield'       => ['shield-check',          '#10B981', '#0d9488'],
        'eye'          => ['eye',                  '#10B981', '#0d9488'],
    ];
}

/**
 * Render an animated icon web component.
 *
 * @param string $name   Icon name (check, chart, zap, etc.)
 * @param int    $size   Icon size in pixels (default: 18)
 * @param string $class  Additional CSS class(es)
 * @return string HTML for the animated icon
 */
function icon(string $name, int $size = 18, string $class = ''): string {
    $map = getIconMap();

    if (!isset($map[$name])) {
        return '';
    }

    [$component, $primary, $secondary] = $map[$name];

    $tag = "animated-lucide-{$component}";
    $cls = 'al-icon-wrapper doji-icon' . ($class ? " {$class}" : '');

    return '<span class="' . $cls . '" style="display:inline-flex;vertical-align:-3px">'
         . '<' . $tag
         . ' size="' . $size . '"'
         . ' primary-color="' . $primary . '"'
         . ' secondary-color="' . $secondary . '"'
         . '></' . $tag . '>'
         . '</span>';
}

/**
 * Get all unique Lucide component names used by the icon map.
 * Used to generate the <script> import tags.
 */
function getIconImports(): array {
    $map = getIconMap();
    $components = [];

    foreach ($map as $entry) {
        $component = $entry[0];
        // Convert kebab-case to PascalCase for the JS import
        $pascal = str_replace(' ', '', ucwords(str_replace('-', ' ', $component)));
        $components[$component] = $pascal;
    }

    return $components;
}

/**
 * Render all <script type="module"> tags for used icons.
 * Call this once in the <head> or before </body>.
 *
 * @param array|null $only  Optional list of icon names to load (null = load all)
 * @return string HTML script tags
 */
function iconScripts(?array $only = null): string {
    $map = getIconMap();
    $components = [];

    // Collect unique components
    foreach ($map as $name => $entry) {
        if ($only !== null && !in_array($name, $only)) {
            continue;
        }
        $component = $entry[0];
        $pascal = str_replace(' ', '', ucwords(str_replace('-', ' ', $component)));
        $components[$pascal] = true;
    }

    $cdn = 'https://cdn.jsdelivr.net/npm/@animated-color-icons/lucide-wc';
    $html = "<!-- Animated Icons (lucide-wc) -->\n";

    foreach (array_keys($components) as $pascal) {
        $html .= '<script type="module" src="' . $cdn . '/' . $pascal . '.js"></script>' . "\n";
    }

    return $html;
}
