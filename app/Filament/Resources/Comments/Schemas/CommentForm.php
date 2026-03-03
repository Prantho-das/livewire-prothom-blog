<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment Verification & Content')
                    ->schema([
                        Select::make('post_id')
                            ->relationship('post', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_bn ?? $record->slug)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty for guest comments'),

                        TextInput::make('name')
                            ->label('Guest Name')
                            ->visible(fn ($get) => ! $get('user_id')),

                        TextInput::make('email')
                            ->email()
                            ->visible(fn ($get) => ! $get('user_id')),

                        Textarea::make('body')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        Toggle::make('is_approved')
                            ->label('Approve for display')
                            ->default(false),
                    ])->columns(2),
            ]);
    }
}
