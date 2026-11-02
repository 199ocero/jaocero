<?php

namespace App\Filament\Resources\Messages\Tables;

use App\Enums\MessageSource;
use App\Models\Message;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')
                    ->label('')
                    ->icon(fn (Message $record): Heroicon => $record->isRead() ? Heroicon::OutlinedEnvelopeOpen : Heroicon::Envelope)
                    ->color(fn (Message $record): string => $record->isRead() ? 'gray' : 'primary'),
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (Message $record): ?string => $record->company)
                    ->weight(fn (Message $record): ?string => $record->isRead() ? null : 'bold'),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('message')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('source')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->options(MessageSource::class),
                TernaryFilter::make('read_at')
                    ->label('Read')
                    ->nullable(),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label('Mark as read')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (Message $record): bool => ! $record->isRead())
                    ->action(fn (Message $record) => $record->markAsRead()),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label('Mark as read')
                        ->icon(Heroicon::OutlinedCheck)
                        ->action(fn (Collection $records) => Message::query()->whereKey($records->modelKeys())->unread()->update(['read_at' => now()]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
