<?php

namespace App\Filament\Resources\ChatConversations\Schemas;

use App\Enums\ChatRole;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChatConversationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Transcript')
                    ->schema([
                        RepeatableEntry::make('messages')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('role')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->color(fn (ChatRole $state): string => $state === ChatRole::User ? 'gray' : 'primary'),
                                TextEntry::make('content')
                                    ->hiddenLabel()
                                    ->markdown(),
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make('Details')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Started')
                            ->dateTime(),
                        TextEntry::make('messages_count')
                            ->label('Messages')
                            ->state(fn ($record): int => $record->messages()->count()),
                        TextEntry::make('user_agent')
                            ->placeholder('-'),
                        TextEntry::make('ip_hash')
                            ->label('Visitor fingerprint')
                            ->helperText('A hash of the IP address. The raw address is never stored.')
                            ->limit(16)
                            ->placeholder('-'),
                    ])
                    ->columnSpan(1),
            ]);
    }
}
