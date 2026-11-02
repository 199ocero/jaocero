<?php

namespace App\Models;

use App\Enums\SkillCategory;
use App\Models\Concerns\ClearsSeoCache;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property SkillCategory $category
 * @property int|null $proficiency 1-100
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'category', 'proficiency', 'sort_order'])]
class Skill extends Model
{
    use ClearsSeoCache;

    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => SkillCategory::class,
            'proficiency' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * All skills grouped by category label, with categories in their defined order.
     *
     * @return Collection<int|string, \Illuminate\Database\Eloquent\Collection<int, static>>
     */
    public static function groupedByCategory(): Collection
    {
        return static::query()->ordered()->get()
            ->sortBy(fn (self $skill): int => $skill->category->position())
            ->groupBy(fn (self $skill): string => $skill->category->label())
            ->toBase();
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }
}
