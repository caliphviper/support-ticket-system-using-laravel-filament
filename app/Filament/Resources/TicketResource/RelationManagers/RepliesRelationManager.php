<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    public function form(Form $form): Form
    {
          return $form->schema([
            Forms\Components\Textarea::make('message')
                ->required()
                ->label('Reply Message'),
            Forms\Components\FileUpload::make('attachment')
                ->directory('ticket-attachments')
                ->label('Attachment')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('message')->wrap()->limit(100),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Replied At'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id(); // Automatically assign current user to reply
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
