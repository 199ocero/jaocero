<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Schemas\ContentFields;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make([
                        ContentFields::title(),
                        ContentFields::slug('projects'),
                        Textarea::make('summary')
                            ->required()
                            ->rows(2)
                            ->maxLength(500),
                        MarkdownEditor::make('body')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('projects/attachments'),
                    ]),
                    ContentFields::seoSection(),
                ])->columnSpan(2),

                Group::make([
                    Section::make('Publishing')->schema([
                        ContentFields::publishedAt(),
                        Toggle::make('is_featured')
                            ->label('Featured on the home page'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ]),
                    Section::make('Details')->schema([
                        FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('projects'),
                        TagsInput::make('tech_stack')
                            ->placeholder('Add a technology'),
                        TextInput::make('github_url')
                            ->label('GitHub URL')
                            ->url(),
                        TextInput::make('live_url')
                            ->label('Live URL')
                            ->url(),
                    ]),
                ])->columnSpan(1),
            ]);
    }
}
