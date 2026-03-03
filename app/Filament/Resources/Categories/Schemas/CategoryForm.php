<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Utilities\Set;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Hierarchy & Settings')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('unique-slug-here'),

                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->relationship(
                                name: 'parent', 
                                titleAttribute: 'slug',
                                modifyQueryUsing: fn (Builder $query) => $query->with('translation')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->translation?->name ?? $record->slug)
                            ->searchable()
                            ->preload()
                            ->placeholder('Select Parent'),

                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true),
                    ])->columns(2),

                Tabs::make('Translations')
                    ->tabs([
                        Tab::make('Bangla')
                            ->schema([
                                TextInput::make('name_bn')
                                    ->label('Bangla Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                Textarea::make('description_bn')
                                    ->label('Bangla Description')
                                    ->rows(3),
                            ]),
                        Tab::make('English')
                            ->schema([
                                TextInput::make('name_en')
                                    ->label('English Name'),
                                Textarea::make('description_en')
                                    ->label('English Description')
                                    ->rows(3),
                            ]),
                    ]),
            ]);
    }
}
