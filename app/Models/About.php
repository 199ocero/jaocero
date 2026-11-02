<?php

namespace App\Models;

use App\Models\Concerns\ClearsSeoCache;
use Database\Factories\AboutFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Singleton holding the owner's profile. Always access it through About::current().
 *
 * @property int $id
 * @property string|null $headline
 * @property string|null $bio
 * @property string|null $location
 * @property string|null $avatar
 * @property string|null $resume
 * @property string|null $og_image
 * @property string|null $github_url
 * @property string|null $linkedin_url
 * @property string|null $x_url
 * @property string|null $email
 * @property bool $open_to_work
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'headline', 'bio', 'location', 'avatar', 'resume', 'og_image',
    'github_url', 'linkedin_url', 'x_url', 'email', 'open_to_work',
])]
class About extends Model
{
    use ClearsSeoCache;

    /** @use HasFactory<AboutFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'open_to_work' => 'boolean',
        ];
    }

    /**
     * Get the single About record, creating an empty one if needed.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    /**
     * The bio split into paragraphs on blank lines.
     *
     * @return list<string>
     */
    public function bioParagraphs(): array
    {
        $paragraphs = preg_split('/\R{2,}/', trim((string) $this->bio)) ?: [];

        return array_values(array_filter($paragraphs, fn (string $paragraph): bool => $paragraph !== ''));
    }

    /**
     * Social profile links that are filled in, keyed by network name.
     *
     * @return array<string, string>
     */
    public function socialLinks(): array
    {
        return array_filter([
            'GitHub' => $this->github_url,
            'LinkedIn' => $this->linkedin_url,
            'X' => $this->x_url,
        ]);
    }

    public function avatarUrl(): ?string
    {
        return $this->publicUrl($this->avatar);
    }

    public function resumeUrl(): ?string
    {
        return $this->publicUrl($this->resume);
    }

    public function ogImageUrl(): ?string
    {
        return $this->publicUrl($this->og_image);
    }

    private function publicUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
