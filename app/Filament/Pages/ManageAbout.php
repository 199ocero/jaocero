<?php

namespace App\Filament\Pages;

use App\Models\About;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Edits the singleton About record: the owner's bio, links and files.
 *
 * @property-read Schema $form
 */
class ManageAbout extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'About me';

    protected static ?string $title = 'About me';

    protected static ?string $slug = 'about';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(About::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(About::current())
            ->statePath('data')
            ->columns(3)
            ->components([
                Section::make('Profile')
                    ->schema([
                        TextInput::make('headline')
                            ->helperText('Shown on the About page. The home page hero uses PORTFOLIO_HEADLINE from your .env.')
                            ->maxLength(255),
                        Textarea::make('bio')
                            ->helperText('Plain text. Blank lines start new paragraphs.')
                            ->rows(8),
                        Grid::make(2)->schema([
                            TextInput::make('location')
                                ->placeholder('e.g. Manila, Philippines or Remote'),
                            Toggle::make('open_to_work')
                                ->label('Open to work')
                                ->inline(false),
                        ]),
                    ])
                    ->columnSpan(2),

                Section::make('Files')
                    ->schema([
                        FileUpload::make('avatar')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('about'),
                        FileUpload::make('resume')
                            ->label('Resume (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public')
                            ->directory('about'),
                        FileUpload::make('og_image')
                            ->label('Social share image')
                            ->helperText('1200×630 recommended. Used when pages are shared on social media.')
                            ->image()
                            ->disk('public')
                            ->directory('about'),
                    ])
                    ->columnSpan(1),

                Section::make('Contact & social links')
                    ->schema([
                        TextInput::make('email')
                            ->email(),
                        TextInput::make('github_url')
                            ->label('GitHub URL')
                            ->url(),
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->url(),
                        TextInput::make('x_url')
                            ->label('X URL')
                            ->url(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
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
        About::current()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Saved')
            ->send();
    }
}
