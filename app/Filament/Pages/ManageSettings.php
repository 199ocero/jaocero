<?php

namespace App\Filament\Pages;

use App\Enums\ChatWidgetPosition;
use App\Enums\RobotHeadShape;
use App\Enums\StickerBreakpoint;
use App\Enums\StickerColor;
use App\Models\SiteSetting;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Edits the singleton SiteSetting record: identity, the AI chat, the floating widget and limits.
 *
 * @property-read Schema $form
 */
class ManageSettings extends Page
{
    private const string HEX_COLOR = '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $title = 'Site settings';

    protected static ?string $slug = 'settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(SiteSetting::current())
            ->statePath('data')
            ->components([
                Tabs::make('Settings')
                    ->persistTabInQueryString()
                    ->tabs([
                        $this->identityTab(),
                        $this->homePageTab(),
                        $this->chatTab(),
                        $this->widgetTab(),
                        $this->limitsTab(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Save')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        SiteSetting::current()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Saved')
            ->send();
    }

    private function identityTab(): Tab
    {
        return Tab::make('Identity')
            ->icon(Heroicon::OutlinedUser)
            ->schema([
                TextInput::make('owner_name')
                    ->label('Your name')
                    ->helperText('Used across the site, the AI assistant\'s instructions and structured data.')
                    ->required()
                    ->maxLength(255),
                Grid::make(2)->schema([
                    Select::make('default_theme')
                        ->helperText('What first-time visitors see. They can switch with the navbar toggle.')
                        ->options([
                            'light' => 'Light (warm paper)',
                            'dark' => 'Dark',
                        ])
                        ->selectablePlaceholder(false)
                        ->required(),
                    TextInput::make('source_url')
                        ->label('Source code URL')
                        ->helperText('Optional link to your fork\'s source code, shown in the footer.')
                        ->url()
                        ->maxLength(255),
                ]),
            ]);
    }

    private function homePageTab(): Tab
    {
        return Tab::make('Home page')
            ->icon(Heroicon::OutlinedHome)
            ->schema([
                Section::make('Headline')
                    ->description('The hero reads "{greeting} {headline}", e.g. "Hi, I\'m Jay — a Laravel developer who builds with AI".')
                    ->schema([
                        TextInput::make('hero_greeting')
                            ->label('Greeting')
                            ->helperText('{name} is replaced with your name.')
                            ->placeholder("Hi, I'm {name} —")
                            ->required()
                            ->maxLength(120),
                        TextInput::make('headline')
                            ->helperText('Also used in structured data, llms.txt and the AI\'s quick answers.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('headline_highlight')
                            ->label('Highlighted phrase')
                            ->helperText('Words from the headline to draw a hand-drawn marker behind, e.g. "AI". Leave empty for no highlight.')
                            ->maxLength(120)
                            ->rule(fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                if (filled($value) && mb_stripos((string) $get('headline'), (string) $value) === false) {
                                    $fail('The highlighted phrase must appear in the headline.');
                                }
                            }),
                        Textarea::make('tagline')
                            ->helperText('The intro under the headline, also used as the home page meta and social description.')
                            ->required()
                            ->rows(3)
                            ->maxLength(500),
                    ]),

                Section::make('Floating notes')
                    ->description('The hand-written notes around the hero. Leave one empty to hide it.')
                    ->schema([
                        TextInput::make('hero_note')
                            ->label('Note above the headline')
                            ->placeholder('hi there!')
                            ->maxLength(60),
                        TextInput::make('hero_chat_note')
                            ->label('Note pointing at the chat')
                            ->placeholder('psst… ask my AI anything')
                            ->maxLength(60),
                        TextInput::make('hero_cursor_label')
                            ->label('Cursor name tag')
                            ->helperText('The wandering design-tool cursor (large screens).')
                            ->placeholder('you?')
                            ->maxLength(30),
                    ])
                    ->columns(3),

                Section::make('Floating stickers')
                    ->description('Tappable stickers floating around the chat. They sit outside the chat column, so they only fit on wide screens.')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('stickers_enabled')
                                ->label('Show the stickers')
                                ->inline(false),
                            Select::make('stickers_breakpoint')
                                ->label('Show on')
                                ->options(StickerBreakpoint::class)
                                ->selectablePlaceholder(false)
                                ->required(),
                        ]),
                        Repeater::make('stickers')
                            ->label('Stickers')
                            ->helperText('Up to four, placed clockwise from the top left. Leave empty to show your first four skills (Content › Skills).')
                            ->schema([
                                TextInput::make('text')
                                    ->required()
                                    ->maxLength(30),
                                Select::make('color')
                                    ->options(StickerColor::class)
                                    ->placeholder('Automatic'),
                            ])
                            ->columns(2)
                            ->maxItems(4)
                            ->defaultItems(0)
                            ->addActionLabel('Add sticker')
                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                            ->collapsible(),
                    ]),
            ]);
    }

    private function chatTab(): Tab
    {
        return Tab::make('AI chat')
            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
            ->schema([
                Section::make('Header')
                    ->description('{name} is replaced with your name.')
                    ->schema([
                        TextInput::make('chat_title')
                            ->label('Title')
                            ->placeholder("{name}'s AI assistant")
                            ->required()
                            ->maxLength(255),
                        TextInput::make('chat_subtitle')
                            ->label('Subtitle')
                            ->placeholder("Answers from {name}'s real projects and experience")
                            ->helperText('Leave empty to hide it.')
                            ->maxLength(255),
                        TextInput::make('assistant_name')
                            ->helperText('How the assistant introduces itself in its answers, e.g. "AI assistant".')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Avatar')
                    ->description('A robot-heads robot whose face shows what the assistant is doing: thinking, speaking or stuck.')
                    ->schema([
                        Grid::make(1)->schema([
                            Select::make('robot_shape')
                                ->label('Shape')
                                ->options(RobotHeadShape::class)
                                ->selectablePlaceholder(false)
                                ->required(),
                            ColorPicker::make('robot_color')
                                ->label('Shell color')
                                ->regex(self::HEX_COLOR)
                                ->required(),
                            ColorPicker::make('robot_trim_color')
                                ->label('Ears and antenna color')
                                ->regex(self::HEX_COLOR)
                                ->required(),
                            ColorPicker::make('robot_screen_color')
                                ->label('Screen LED color')
                                ->regex(self::HEX_COLOR)
                                ->required(),
                        ])->columnSpan(2),
                        View::make('filament.robot-head-preview'),
                    ])
                    ->columns(3),

                Section::make('Model')
                    ->schema([
                        TextInput::make('chat_model')
                            ->label('Model')
                            ->helperText(fn (): string => 'Must be a model of the "'.config('portfolio.chat.provider').'" provider. The provider and its API key are set in .env (AI_PROVIDER).')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('chat_timeout')
                            ->label('Timeout')
                            ->helperText('Seconds to wait for the provider before showing a friendly error.')
                            ->suffix('seconds')
                            ->integer()
                            ->minValue(5)
                            ->maxValue(300)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    private function widgetTab(): Tab
    {
        return Tab::make('Floating chat')
            ->icon(Heroicon::OutlinedChatBubbleOvalLeftEllipsis)
            ->schema([
                Toggle::make('widget_enabled')
                    ->label('Show the floating chat')
                    ->helperText('The chat button on every page except home, which has the chat built into the hero.'),
                Grid::make(2)->schema([
                    Select::make('widget_position')
                        ->label('Position')
                        ->options(ChatWidgetPosition::class)
                        ->selectablePlaceholder(false)
                        ->required(),
                    TextInput::make('widget_label')
                        ->label('Button label')
                        ->required()
                        ->maxLength(40),
                    TextInput::make('widget_note')
                        ->label('Hand-written note')
                        ->helperText('Appears next to the button after a few seconds. Leave empty to hide it.')
                        ->maxLength(60),
                ]),
            ]);
    }

    private function limitsTab(): Tab
    {
        return Tab::make('Limits & SEO')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->schema([
                Section::make('Chat limits')
                    ->description('Cost and abuse limits, applied per visitor (by IP).')
                    ->schema([
                        $this->limitInput('chat_max_message_length', 'Max message length', 'characters'),
                        $this->limitInput('chat_max_turns', 'Questions per conversation', 'questions'),
                        $this->limitInput('chat_rate_limit_per_minute', 'Messages per minute', 'messages'),
                        $this->limitInput('chat_rate_limit_per_day', 'Messages per day', 'messages'),
                    ])
                    ->columns(2),

                Section::make('Contact form')
                    ->schema([
                        $this->limitInput('contact_rate_limit', 'Messages per visitor', 'messages'),
                        $this->limitInput('contact_rate_limit_decay_minutes', 'Within', 'minutes'),
                    ])
                    ->columns(2),

                Section::make('AI discoverability')
                    ->schema([
                        Toggle::make('allow_ai_training')
                            ->label('Allow AI training crawlers')
                            ->helperText('AI search bots (ChatGPT search, Claude, Perplexity...) are always allowed so your site can be cited. Turn this off to block training crawlers (GPTBot, ClaudeBot, Google-Extended, CCBot...) in /robots.txt.'),
                    ]),
            ]);
    }

    private function limitInput(string $name, string $label, string $suffix): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->suffix($suffix)
            ->integer()
            ->minValue(1)
            ->maxValue(100000)
            ->required();
    }
}
