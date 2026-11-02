<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * The screen width from which the floating stickers appear. They sit outside the chat column,
 * so narrower screens have no room for them.
 */
enum StickerBreakpoint: string implements HasLabel
{
    case ExtraLarge = 'xl';
    case DoubleExtraLarge = '2xl';

    public function getLabel(): string
    {
        return match ($this) {
            self::ExtraLarge => 'Large screens (1280px and wider)',
            self::DoubleExtraLarge => 'Extra large screens (1536px and wider)',
        };
    }
}
