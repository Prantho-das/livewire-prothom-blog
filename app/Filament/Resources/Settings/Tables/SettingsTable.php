<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site_name')
                    ->label('Site Name')
                    ->searchable(),
                
                TextColumn::make('site_title')
                    ->label('Site Title')
                    ->searchable(),
                    
                ImageColumn::make('logo')
                    ->label('Logo'),
                    
                TextColumn::make('contact_email')
                    ->label('Contact Email')
                    ->searchable(),
                    
                TextColumn::make('contact_phone')
                    ->label('Contact Phone')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
