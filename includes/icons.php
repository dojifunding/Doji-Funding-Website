<?php
/**
 * Doji Funding — SVG Icon Helpers
 * Replaces emoji with clean inline SVG icons
 */

function icon($name, $size = 18, $class = '') {
    $c = $class ? " class=\"$class\"" : '';
    $icons = [
        'check' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><polyline points="20 6 9 17 4 12"/></svg>',
        'check-circle' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><circle cx="12" cy="12" r="10"/><polyline points="9 12 12 15 16 10"/></svg>',
        'x-circle' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#ff3b3b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        'target' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
        'chart' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
        'trending' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
        'calendar' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'bot' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><rect x="4" y="4" width="16" height="16" rx="2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/><path d="M9 14h6"/></svg>',
        'coins' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><circle cx="12" cy="12" r="8"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="9" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="15" y2="14"/></svg>',
        'message' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
        'diamond' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><path d="M6 3h12l4 6-10 13L2 9z"/><path d="M2 9h20"/></svg>',
        'zap' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#ff9f1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
        'crown' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#ff9f1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><path d="M2 20h20L19 8l-5 5-2-7-2 7-5-5z"/></svg>',
        'circle-green' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" style="display:inline;vertical-align:-3px"><circle cx="12" cy="12" r="6" fill="#10B981"/></svg>',
        'info' => '<svg'.$c.' width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="#4a9eff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:-3px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    ];
    return $icons[$name] ?? '';
}
