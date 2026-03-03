<?php

namespace App\Filament\Resources\EPapers\Pages;

use App\Filament\Resources\EPapers\EPaperResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEPapers extends ListRecords
{
    protected static string $resource = EPaperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
