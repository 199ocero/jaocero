<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * The screen corner the floating chat widget sits in.
 */
enum ChatWidgetPosition: string implements HasLabel
{
    case BottomRight = 'bottom-right';
    case BottomLeft = 'bottom-left';

    public function getLabel(): string
    {
        return match ($this) {
            self::BottomRight => 'Bottom right',
            self::BottomLeft => 'Bottom left',
        };
    }
}
