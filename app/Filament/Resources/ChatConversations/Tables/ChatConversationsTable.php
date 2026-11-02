<?php

namespace App\Filament\Resources\ChatConversations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChatConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withCount('messages')
                ->with(['messages' => fn ($messages) => $messages->where('role', 'user')->limit(1)]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('first_question')
                    ->label('First question')
                    ->state(fn ($record): ?string => $record->messages->first()?->content)
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Started')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
