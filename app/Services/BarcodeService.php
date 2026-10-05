<?php

namespace App\Services;

class BarcodeService
{
    /**
     * Code 128B patterns (107 patterns, indexed by character code 0..106)
     */
    private static array $patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112' // 106 is STOP pattern
    ];

    /**
     * Generate Code 128 SVG markup
     *
     * @param string $text
     * @param int $height Height of barcode in pixels
     * @param float $scale Width scale factor
     * @param bool $showText Whether to display text under barcode
     * @return string Raw SVG markup
     */
    public static function getBarcodeSVG(string $text, int $height = 50, float $scale = 1.4, bool $showText = true): string
    {
        $text = trim($text);
        if (empty($text)) {
            $text = '00000';
        }

        // Code 128B start character code is 104
        $startCode = 104;
        $checksum = $startCode;
        $codes = [$startCode];

        for ($i = 0; $i < strlen($text); $i++) {
            $ascii = ord($text[$i]);
            // Code 128B character value = ascii - 32
            $val = $ascii - 32;
            if ($val < 0 || $val > 95) {
                $val = 0; // fallback space
            }
            $codes[] = $val;
            $checksum += $val * ($i + 1);
        }

        // Checksum modulo 103
        $checksumValue = $checksum % 103;
        $codes[] = $checksumValue;

        // Stop character code is 106
        $codes[] = 106;

        // Build bars sequence
        $bars = '';
        foreach ($codes as $code) {
            $pattern = self::$patterns[$code] ?? self::$patterns[0];
            $isBar = true;
            for ($p = 0; $p < strlen($pattern); $p++) {
                $width = (int)$pattern[$p];
                $bars .= str_repeat($isBar ? '1' : '0', $width);
                $isBar = !$isBar;
            }
        }

        // Add quiet zones (10 modules on each side)
        $quietZone = 10;
        $totalModules = strlen($bars) + ($quietZone * 2);
        $svgWidth = round($totalModules * $scale, 1);
        $totalHeight = $showText ? ($height + 16) : $height;

        $svg = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$svgWidth}\" height=\"{$totalHeight}\" viewBox=\"0 0 {$svgWidth} {$totalHeight}\">\n";
        $svg .= "  <rect width=\"100%\" height=\"100%\" fill=\"#ffffff\"/>\n";
        $svg .= "  <g fill=\"#000000\">\n";

        $x = $quietZone * $scale;
        $barStart = null;

        for ($i = 0; $i < strlen($bars); $i++) {
            $currX = ($quietZone + $i) * $scale;
            $w = $scale;
            if ($bars[$i] === '1') {
                $svg .= "    <rect x=\"{$currX}\" y=\"2\" width=\"{$w}\" height=\"{$height}\"/>\n";
            }
        }

        $svg .= "  </g>\n";

        if ($showText) {
            $textY = $height + 13;
            $centerX = $svgWidth / 2;
            $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            $svg .= "  <text x=\"{$centerX}\" y=\"{$textY}\" font-family=\"monospace, sans-serif\" font-size=\"11\" font-weight=\"bold\" text-anchor=\"middle\" fill=\"#000000\" letter-spacing=\"1\">{$safeText}</text>\n";
        }

        $svg .= "</svg>";

        return $svg;
    }
}
