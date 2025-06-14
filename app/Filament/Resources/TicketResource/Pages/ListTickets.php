<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')->searchable(),
            TextColumn::make('user.name')->label('User'),
            TextColumn::make('priority')->badge()->color(fn ($state) => match($state) {
                'high' => 'danger',
                'medium' => 'warning',
                'low' => 'success',
            }),
            TextColumn::make('status')->badge()->color(fn ($state) => match($state) {
                'open' => 'gray',
                'in_progress' => 'info',
                'closed' => 'success',
            }),
            TextColumn::make('created_at')->dateTime(),
        ];
    }
}
