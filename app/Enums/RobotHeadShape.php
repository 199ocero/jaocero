<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Outline of the chat assistant's robot-heads avatar (https://www.npmjs.com/package/robot-heads).
 */
enum RobotHeadShape: string implements HasLabel
{
    case Rectangle = 'rectangle';
    case Square = 'square';
    case Circle = 'circle';
    case Hexagon = 'hexagon';

    public function getLabel(): string
    {
        return match ($this) {
            self::Rectangle => 'Rectangle (classic TV)',
            self::Square => 'Square',
            self::Circle => 'Circle (porthole)',
            self::Hexagon => 'Hexagon (nut)',
        };
    }
}
