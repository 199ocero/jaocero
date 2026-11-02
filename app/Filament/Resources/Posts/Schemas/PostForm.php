<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Schemas\ContentFields;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make([
                        ContentFields::title(),
                        ContentFields::slug('posts'),
                        Textarea::make('excerpt')
                            ->required()
                            ->rows(2)
                            ->maxLength(500),
                        MarkdownEditor::make('body')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/attachments'),
                    ]),
                    ContentFields::seoSection(),
                ])->columnSpan(2),

                Group::make([
                    Section::make('Publishing')->schema([
                        ContentFields::publishedAt(),
                        TagsInput::make('tags')
                            ->placeholder('Add a tag'),
                    ]),
                    Section::make('Cover')->schema([
                        FileUpload::make('cover_image')
                            ->hiddenLabel()
                            ->image()
                            ->disk('public')
                            ->directory('posts'),
                    ]),
                ])->columnSpan(1),
            ]);
    }
}
