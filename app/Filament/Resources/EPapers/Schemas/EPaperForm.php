<?php

namespace App\Filament\Resources\EPapers\Schemas;

use Filament\Schemas\Schema;

class EPaperForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('E-Paper Upload')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('edition_date')
                            ->required()
                            ->default(now()),
                            
                        \Filament\Forms\Components\FileUpload::make('pdf_path')
                            ->label('PDF File')
                            ->directory('epapers')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),

                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true),
                    ])->columns(2),

                \Filament\Schemas\Components\Tabs::make('Translations')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Bangla')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('title_bn')
                                    ->label('Bangla Title')
                                    ->required(),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('English')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('title_en')
                                    ->label('English Title'),
                            ]),
                    ]),
            ]);
    }
}
