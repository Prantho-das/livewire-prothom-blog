<?php

namespace App\Filament\Resources\EPapers\Pages;

use App\Filament\Resources\EPapers\EPaperResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEPaper extends EditRecord
{
    protected static string $resource = EPaperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
