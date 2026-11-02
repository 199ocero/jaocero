<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make([
                    TextEntry::make('message')
                        ->hiddenLabel()
                        ->prose(),
                ])->columnSpan(2),

                Section::make([
                    TextEntry::make('name'),
                    TextEntry::make('email')
                        ->copyable()
                        ->url(fn ($record): string => 'mailto:'.$record->email),
                    TextEntry::make('company')
                        ->placeholder('-'),
                    TextEntry::make('source')
                        ->badge(),
                    TextEntry::make('created_at')
                        ->label('Received')
                        ->dateTime(),
                    TextEntry::make('read_at')
                        ->label('Read')
                        ->dateTime()
                        ->placeholder('Unread'),
                ])->columnSpan(1),
            ]);
    }
}
