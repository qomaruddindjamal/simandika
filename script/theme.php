<?php
/**
 * Mesin fase tema JIBAS.
 * Fase dihitung server ( timezone Asia/Jakarta, tidak mengubah global TZ ) lalu bisa dikoreksi klien.
 * Aturan: 06:00-16:59 terang | 17:00-17:59 senja | 18:00-03:59 gelap | 04:00-05:59 fajar.
 */

if (!function_exists('jibas_phases')) {
    function jibas_phases()
    {
        return array(
            'terang' => array('base' => 'light', 'label' => 'Terang', 'icon' => 'bi-sun',     'range' => '06:00-16:59', 'theme_color' => '#0f6fb8'),
            'senja'  => array('base' => 'dark',  'label' => 'Senja',  'icon' => 'bi-sunset',  'range' => '17:00-17:59', 'theme_color' => '#ff8a5b'),
            'gelap'  => array('base' => 'dark',  'label' => 'Gelap',  'icon' => 'bi-moon-stars', 'range' => '18:00-03:59', 'theme_color' => '#0e1319'),
            'fajar'  => array('base' => 'light', 'label' => 'Fajar',  'icon' => 'bi-sunrise', 'range' => '04:00-05:59', 'theme_color' => '#7c5cad'),
        );
    }
}

if (!function_exists('jibas_now_hour')) {
    function jibas_now_hour()
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        return (int)$now->format('G');
    }
}

if (!function_exists('jibas_phase')) {
    function jibas_phase($hour = null)
    {
        $h = is_null($hour) ? jibas_now_hour() : (int)$hour;
        if ($h >= 6 && $h <= 16) return 'terang';
        if ($h === 17) return 'senja';
        if ($h >= 18 || $h <= 3) return 'gelap';
        return 'fajar';
    }
}

if (!function_exists('jibas_phase_base')) {
    function jibas_phase_base($phase)
    {
        $phases = jibas_phases();
        return isset($phases[$phase]) ? $phases[$phase]['base'] : 'light';
    }
}

if (!function_exists('jibas_theme_mode')) {
    /** Mode pilihan user: auto | terang | gelap. Mode manual selalu terang/gelap (senja & fajar hanya di auto). */
    function jibas_theme_mode($mode)
    {
        $mode = is_string($mode) ? strtolower($mode) : '';
        return in_array($mode, array('auto', 'terang', 'gelap'), true) ? $mode : 'auto';
    }
}

if (!function_exists('jibas_resolve_phase')) {
    function jibas_resolve_phase($mode, $hour = null)
    {
        $mode = jibas_theme_mode($mode);
        if ($mode === 'terang') return 'terang';
        if ($mode === 'gelap') return 'gelap';
        return jibas_phase($hour);
    }
}

if (!function_exists('jibas_theme_attrs')) {
    /** Atribut untuk tag <html>. $simulate hanya dipakai halaman pratinjau, nilainya di-whitelist. */
    function jibas_theme_attrs($mode = 'auto', $simulate = null)
    {
        $phase = in_array($simulate, array('terang', 'senja', 'gelap', 'fajar'), true)
            ? $simulate
            : jibas_resolve_phase($mode);
        return 'data-bs-theme="' . jibas_phase_base($phase) . '" data-jibas-phase="' . $phase . '"';
    }
}

if (!function_exists('jibas_phase_info')) {
    function jibas_phase_info($phase)
    {
        $phases = jibas_phases();
        return isset($phases[$phase]) ? $phases[$phase] : $phases['terang'];
    }
}
