<?php

use App\Enums\ChatWidgetPosition;
use App\Enums\RobotHeadShape;
use App\Enums\StickerBreakpoint;
use App\Enums\StickerColor;
use App\Filament\Pages\ManageSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    config()->set('portfolio.admin_emails', ['owner@example.com']);

    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']));
});

it('starts from the config defaults', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');

    Livewire::test(ManageSettings::class)
        ->assertSchemaStateSet(['owner_name' => 'Sam Rivera', 'chat_max_turns' => 20]);
});

it('saves the settings to the single record and applies them to the site', function () {
    Livewire::test(ManageSettings::class)
        ->fillForm([
            'owner_name' => 'Sam Rivera',
            'robot_shape' => RobotHeadShape::Hexagon,
            'robot_color' => '#ff6600',
            'widget_position' => ChatWidgetPosition::BottomLeft,
            'widget_label' => 'Chat with me',
            'chat_rate_limit_per_day' => 5,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect(SiteSetting::count())->toBe(1)
        ->and(SiteSetting::current())
        ->owner_name->toBe('Sam Rivera')
        ->robot_shape->toBe(RobotHeadShape::Hexagon)
        ->and(config('portfolio.chat.rate_limit.per_day'))->toBe(5);

    $this->get(route('about'))
        ->assertSee('Sam Rivera')
        ->assertSee('Chat with me')
        ->assertSee('shape="hexagon"', false)
        ->assertSee('color="#ff6600"', false);
});

it('rejects invalid settings', function (string $field, mixed $value, string $rule) {
    Livewire::test(ManageSettings::class)
        ->fillForm([$field => $value])
        ->call('save')
        ->assertHasFormErrors([$field => $rule]);
})->with([
    'missing name' => ['owner_name', '', 'required'],
    'color that is not hex' => ['robot_color', 'rgb(0, 0, 0)', 'regex'],
    'zero chat turns' => ['chat_max_turns', 0, 'min'],
    'invalid source url' => ['source_url', 'not a url', 'url'],
]);

it('requires the highlighted phrase to appear in the headline', function () {
    Livewire::test(ManageSettings::class)
        ->fillForm(['headline' => 'a Laravel developer', 'headline_highlight' => 'Rust'])
        ->call('save')
        ->assertHasFormErrors(['headline_highlight']);
});

it('saves the floating stickers', function () {
    Livewire::test(ManageSettings::class)
        ->fillForm([
            'stickers_enabled' => true,
            'stickers_breakpoint' => StickerBreakpoint::DoubleExtraLarge,
            'stickers' => [
                ['text' => 'Laravel AI SDK', 'color' => StickerColor::Sky],
                ['text' => 'Livewire', 'color' => null],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(SiteSetting::current())
        ->stickers_breakpoint->toBe(StickerBreakpoint::DoubleExtraLarge)
        ->and(array_column(SiteSetting::current()->stickers, 'text'))->toBe(['Laravel AI SDK', 'Livewire']);

    $this->get(route('home'))->assertSeeInOrder(['Laravel AI SDK', 'Livewire']);
});

it('allows at most four stickers', function () {
    Livewire::test(ManageSettings::class)
        ->fillForm([
            'stickers' => collect(range(1, 5))->map(fn (int $number): array => ['text' => "Sticker {$number}", 'color' => null])->all(),
        ])
        ->call('save')
        ->assertHasFormErrors(['stickers']);
});
