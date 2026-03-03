<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tag Identity')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),

                Tabs::make('Translations')
                    ->tabs([
                        Tab::make('Bangla')
                            ->schema([
                                TextInput::make('name_bn')
                                    ->label('Name (BN)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                            ]),
                        Tab::make('English')
                            ->schema([
                                TextInput::make('name_en')
                                    ->label('Name (EN)'),
                            ]),
                    ]),
            ]);
    }
}
