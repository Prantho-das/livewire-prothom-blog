<?php

namespace App\Filament\Resources\Notices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class NoticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('body')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
                        'topbar' => 'Topbar Banner',
                        'popup' => 'Popup',
                        'slide' => 'Slide-in Notice',
                        'alert' => 'Alert Box',
                    ])
                    ->required()
                    ->default('topbar'),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date'),
            ]);
    }
}
