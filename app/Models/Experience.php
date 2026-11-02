<?php

namespace App\Models;

use App\Models\Concerns\ClearsSeoCache;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $company
 * @property string $role
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['company', 'role', 'start_date', 'end_date', 'description'])]
class Experience extends Model
{
    use ClearsSeoCache;

    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * A null end date means the owner still works here.
     */
    public function isCurrent(): bool
    {
        return $this->end_date === null;
    }

    /**
     * Human-friendly period, e.g. "Mar 2022 – Present".
     */
    public function period(): string
    {
        $end = $this->end_date?->format('M Y') ?? 'Present';

        return $this->start_date->format('M Y').' – '.$end;
    }

    /**
     * Current roles first, then most recent.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function latestFirst(Builder $query): void
    {
        $query->orderByRaw('end_date is not null')
            ->orderByDesc('start_date');
    }
}
