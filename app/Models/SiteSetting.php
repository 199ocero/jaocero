<?php

namespace App\Models;

use App\Enums\ChatWidgetPosition;
use App\Enums\RobotHeadShape;
use App\Enums\StickerBreakpoint;
use App\Models\Concerns\ClearsSeoCache;
use BackedEnum;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Singleton holding the site settings edited in the admin panel. Always access it through SiteSetting::current().
 *
 * Every column overrides one config('portfolio.*') value (see CONFIG_KEYS), so the rest of the app keeps
 * reading config(). config/portfolio.php holds the defaults used before the settings are first saved.
 *
 * @property int $id
 * @property string|null $owner_name
 * @property string|null $hero_greeting
 * @property string|null $headline
 * @property string|null $headline_highlight
 * @property string|null $tagline
 * @property string|null $hero_note
 * @property string|null $hero_chat_note
 * @property string|null $hero_cursor_label
 * @property bool|null $stickers_enabled
 * @property list<array{text: string, color: string|null}>|null $stickers
 * @property StickerBreakpoint|null $stickers_breakpoint
 * @property string|null $default_theme
 * @property string|null $source_url
 * @property string|null $assistant_name
 * @property string|null $chat_title
 * @property string|null $chat_subtitle
 * @property string|null $chat_model
 * @property int|null $chat_timeout
 * @property int|null $chat_max_message_length
 * @property int|null $chat_max_turns
 * @property int|null $chat_rate_limit_per_minute
 * @property int|null $chat_rate_limit_per_day
 * @property RobotHeadShape|null $robot_shape
 * @property string|null $robot_color
 * @property string|null $robot_trim_color
 * @property string|null $robot_screen_color
 * @property bool|null $widget_enabled
 * @property ChatWidgetPosition|null $widget_position
 * @property string|null $widget_label
 * @property string|null $widget_note
 * @property int|null $contact_rate_limit
 * @property int|null $contact_rate_limit_decay_minutes
 * @property bool|null $allow_ai_training
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'owner_name', 'hero_greeting', 'headline', 'headline_highlight', 'tagline',
    'hero_note', 'hero_chat_note', 'hero_cursor_label', 'stickers_enabled', 'stickers', 'stickers_breakpoint',
    'default_theme', 'source_url',
    'assistant_name', 'chat_title', 'chat_subtitle', 'chat_model', 'chat_timeout',
    'chat_max_message_length', 'chat_max_turns', 'chat_rate_limit_per_minute', 'chat_rate_limit_per_day',
    'robot_shape', 'robot_color', 'robot_trim_color', 'robot_screen_color',
    'widget_enabled', 'widget_position', 'widget_label', 'widget_note',
    'contact_rate_limit', 'contact_rate_limit_decay_minutes', 'allow_ai_training',
])]
class SiteSetting extends Model
{
    use ClearsSeoCache;

    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    /**
     * The config key each column overrides.
     *
     * @var array<string, string>
     */
    public const array CONFIG_KEYS = [
        'owner_name' => 'portfolio.owner_name',
        'hero_greeting' => 'portfolio.hero.greeting',
        'headline' => 'portfolio.headline',
        'headline_highlight' => 'portfolio.headline_highlight',
        'tagline' => 'portfolio.tagline',
        'hero_note' => 'portfolio.hero.note',
        'hero_chat_note' => 'portfolio.hero.chat_note',
        'hero_cursor_label' => 'portfolio.hero.cursor_label',
        'stickers_enabled' => 'portfolio.hero.stickers.enabled',
        'stickers' => 'portfolio.hero.stickers.items',
        'stickers_breakpoint' => 'portfolio.hero.stickers.breakpoint',
        'default_theme' => 'portfolio.default_theme',
        'source_url' => 'portfolio.source_url',
        'assistant_name' => 'portfolio.assistant_name',
        'chat_title' => 'portfolio.chat.title',
        'chat_subtitle' => 'portfolio.chat.subtitle',
        'chat_model' => 'portfolio.chat.model',
        'chat_timeout' => 'portfolio.chat.timeout',
        'chat_max_message_length' => 'portfolio.chat.max_message_length',
        'chat_max_turns' => 'portfolio.chat.max_turns',
        'chat_rate_limit_per_minute' => 'portfolio.chat.rate_limit.per_minute',
        'chat_rate_limit_per_day' => 'portfolio.chat.rate_limit.per_day',
        'robot_shape' => 'portfolio.chat.avatar.shape',
        'robot_color' => 'portfolio.chat.avatar.color',
        'robot_trim_color' => 'portfolio.chat.avatar.trim_color',
        'robot_screen_color' => 'portfolio.chat.avatar.screen_color',
        'widget_enabled' => 'portfolio.chat_widget.enabled',
        'widget_position' => 'portfolio.chat_widget.position',
        'widget_label' => 'portfolio.chat_widget.label',
        'widget_note' => 'portfolio.chat_widget.note',
        'contact_rate_limit' => 'portfolio.contact.rate_limit.max_attempts',
        'contact_rate_limit_decay_minutes' => 'portfolio.contact.rate_limit.decay_minutes',
        'allow_ai_training' => 'portfolio.seo.allow_ai_training',
    ];

    /**
     * Optional settings where blank means "turned off" rather than "use the default".
     *
     * @var list<string>
     */
    private const array CLEARABLE_COLUMNS = [
        'source_url', 'headline_highlight', 'hero_note', 'hero_chat_note', 'hero_cursor_label', 'chat_subtitle', 'widget_note',
    ];

    protected static function booted(): void
    {
        // Apply changes to the current request too, so the page that saved them shows them straight away.
        static::saved(fn (self $settings) => $settings->applyToConfig());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stickers_enabled' => 'boolean',
            'stickers' => 'array',
            'stickers_breakpoint' => StickerBreakpoint::class,
            'chat_timeout' => 'integer',
            'chat_max_message_length' => 'integer',
            'chat_max_turns' => 'integer',
            'chat_rate_limit_per_minute' => 'integer',
            'chat_rate_limit_per_day' => 'integer',
            'robot_shape' => RobotHeadShape::class,
            'widget_enabled' => 'boolean',
            'widget_position' => ChatWidgetPosition::class,
            'contact_rate_limit' => 'integer',
            'contact_rate_limit_decay_minutes' => 'integer',
            'allow_ai_training' => 'boolean',
        ];
    }

    /**
     * Get the single settings record, creating it from the config defaults if needed.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    /**
     * The current config values, keyed by column.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return array_map(fn (string $configKey): mixed => config($configKey), self::CONFIG_KEYS);
    }

    /**
     * Override config('portfolio.*') with these settings. Blank columns keep the config default.
     */
    public function applyToConfig(): void
    {
        foreach (self::CONFIG_KEYS as $column => $configKey) {
            // Columns added by a migration that has not run yet.
            if (! array_key_exists($column, $this->getAttributes())) {
                continue;
            }

            $value = $this->getAttribute($column);

            if ($value === null && ! in_array($column, self::CLEARABLE_COLUMNS, true)) {
                continue;
            }

            config()->set($configKey, $value instanceof BackedEnum ? $value->value : $value);
        }
    }
}
