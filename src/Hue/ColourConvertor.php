<?php

namespace Xantios\Lamp\Hue;

class ColourConvertor
{

    // Thanks to Mikjaer at StackOverlow
    // https://stackoverflow.com/questions/22894498/philips-hue-convert-xy-from-api-to-hex-or-rgb
    public static function FromXY(float $x, float $y, int $brightness = 254): array
    {

        // print "Converting from X,Y,B to RGB: ".$x.",".$y." => ".$brightness."\n";

        $z = 1.0 - $x - $y;
        $Y = $brightness / 255.0;
        $X = ($Y / $y) * $x;
        $Z = ($Y / $y) * $z;

        $r = $X * 1.612 - $Y * 0.203 - $Z * 0.302;
        $g = ($X * -1) * 0.509 + $Y * 1.412 + $Z * 0.066;
        $b = $X * 0.026 - $Y * 0.072 + $Z * 0.962;

        $r = $r <= 0.0031308 ? 12.92 * $r : (1.0 + 0.055) * pow($r, (1.0 / 2.4)) - 0.055;
        $g = $g <= 0.0031308 ? 12.92 * $g : (1.0 + 0.055) * pow($g, (1.0 / 2.4)) - 0.055;
        $b = $b <= 0.0031308 ? 12.92 * $b : (1.0 + 0.055) * pow($b, (1.0 / 2.4)) - 0.055;

        $maxValue = max($r, $g, $b);

        $r = $r / $maxValue;
        $g = $g / $maxValue;
        $b = $b / $maxValue;

        $r = $r * 255;
        if ($r < 0) $r = 255;
        $g = $g * 255;
        if ($g < 0) $g = 255;
        $b = $b * 255;
        if ($b < 0) $b = 255;

        $r = dechex(round($r));
        $g = dechex(round($g));
        $b = dechex(round($b));

        if (strlen($r) < 2) $r = "0" + $r;
        if (strlen($g) < 2) $g = "0" + $g;
        if (strlen($b) < 2) $b = "0" + $b;

        return [$r, $g, $b];
    }

}