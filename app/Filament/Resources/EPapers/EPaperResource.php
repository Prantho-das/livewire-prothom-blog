<?php

namespace App\Filament\Resources\EPapers;

use App\Filament\Resources\EPapers\Pages\CreateEPaper;
use App\Filament\Resources\EPapers\Pages\EditEPaper;
use App\Filament\Resources\EPapers\Pages\ListEPapers;
use App\Filament\Resources\EPapers\Schemas\EPaperForm;
use App\Filament\Resources\EPapers\Tables\EPapersTable;
use App\Models\EPaper;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EPaperResource extends Resource
{
    protected static ?string $model = EPaper::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static \UnitEnum|string|null $navigationGroup = 'Publications';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EPaperForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EPapersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEPapers::route('/'),
            'create' => CreateEPaper::route('/create'),
            'edit' => EditEPaper::route('/{record}/edit'),
        ];
    }
}
