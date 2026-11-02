<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Paper colors of the floating stickers around the home page chat.
 */
enum StickerColor: string implements HasLabel
{
    case Sky = 'sky';
    case Amber = 'amber';
    case Lime = 'lime';
    case Rose = 'rose';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    /**
     * The color used for the sticker at a position when none is picked, cycling through the cases.
     */
    public static function forPosition(int $position): self
    {
        return self::cases()[$position % count(self::cases())];
    }
}
