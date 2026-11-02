<?php

namespace App\Filament\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/**
 * Form fields shared by publishable content (projects and blog posts).
 */
class ContentFields
{
    /**
     * Title input that fills in the slug while the slug is still untouched.
     */
    public static function title(): TextInput
    {
        return TextInput::make('title')
            ->required()
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    public static function slug(string $table): TextInput
    {
        return TextInput::make('slug')
            ->required()
            ->maxLength(255)
            ->alphaDash()
            ->unique($table, 'slug', ignoreRecord: true);
    }

    public static function publishedAt(): DateTimePicker
    {
        return DateTimePicker::make('published_at')
            ->label('Publish date')
            ->helperText('Leave empty to keep this as a draft. Future dates are scheduled.');
    }

    /**
     * Optional overrides for search engines and social previews.
     */
    public static function seoSection(): Section
    {
        return Section::make('SEO')
            ->description('Optional. Falls back to the title and summary when empty.')
            ->collapsed()
            ->schema([
                TextInput::make('seo_title')
                    ->maxLength(70),
                Textarea::make('seo_description')
                    ->rows(2)
                    ->maxLength(320),
            ]);
    }
}
