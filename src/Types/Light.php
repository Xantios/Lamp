<?php

namespace Xantios\Lamp\Types;

use Xantios\Lamp\Hue\ColourConvertor;

class Light {

    public string $name;
    public string $group = "";
    public bool $on = false;

    // Brightness (also called luminance in old docs),Hue (color) and Saturation
    public int $brightness = 0;
    public float $x = 0;
    public float $y = 0;

    public function GetColour() :array {
        return ColourConvertor::FromXY($this->x,$this->y,$this->brightness);
    }
}