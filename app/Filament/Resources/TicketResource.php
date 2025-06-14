<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, Textarea, Select};
use Filament\Tables\Actions\Action;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required(),
            Textarea::make('description')->required()->rows(5),
            Select::make('priority')
                ->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ])
                ->default('medium')
                ->required(),

            Select::make('status')
                ->options([
                    'open' => 'Open',
                    'in_progress' => 'In Progress',
                    'closed' => 'Closed',
                ])
                ->default('open')
                ->required(),

            Select::make('assigned_admin_id')
                ->label('Assigned To')
                ->relationship('assignedAdmin', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('priority')->badge()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('assignedAdmin.name')->label('Assigned To')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->form([
                        Textarea::make('message')
                            ->label('Your Reply')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (array $data, \App\Models\Ticket $record): void {
                        $record->replies()->create([
                            'message' => $data['message'],
                            'user_id' => auth()->id(),
                        ]);
                    })
                    ->modalHeading('Reply to Ticket')
                    ->modalSubmitActionLabel('Send Reply')
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\TicketResource\RelationManagers\RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
