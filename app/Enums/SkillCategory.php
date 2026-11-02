<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SkillCategory: string implements HasLabel
{
    case Backend = 'backend';
    case Frontend = 'frontend';
    case DevOps = 'devops';
    case AI = 'ai';
    case Database = 'database';
    case Testing = 'testing';
    case Tools = 'tools';
    case Other = 'other';

    /**
     * Human-readable label shown on the site and in the admin panel.
     */
    public function label(): string
    {
        return match ($this) {
            self::Backend => 'Backend',
            self::Frontend => 'Frontend',
            self::DevOps => 'DevOps',
            self::AI => 'AI',
            self::Database => 'Databases',
            self::Testing => 'Testing',
            self::Tools => 'Tools',
            self::Other => 'Other',
        };
    }

    /**
     * Position of the category on the site, following the order of the cases above.
     */
    public function position(): int
    {
        return (int) array_search($this, self::cases(), true);
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
