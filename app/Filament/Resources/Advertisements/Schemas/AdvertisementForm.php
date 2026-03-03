<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AdvertisementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('url')
                    ->url(),
                FileUpload::make('image')
                    ->image(),
                Textarea::make('code')
                    ->columnSpanFull(),
                Select::make('position')
                    ->options([
                        'header' => 'Header',
                        'sidebar' => 'Sidebar',
                        'footer' => 'Footer',
                        'popup' => 'Popup',
                        'in_article' => 'In Article (Between Posts)',
                    ])
                    ->required()
                    ->default('sidebar'),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date'),
            ]);
    }
}
