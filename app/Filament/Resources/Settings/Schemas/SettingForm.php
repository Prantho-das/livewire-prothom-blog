<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Site Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('site_title')
                                    ->label('Site Title')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('site_detail')
                                    ->label('Site Detail')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                FileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('settings'),
                                FileUpload::make('favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->directory('settings'),
                            ])->columns(2),

                        Tab::make('SEO')
                            ->schema([
                                TextInput::make('seo_title')
                                    ->label('SEO Title')
                                    ->maxLength(255),
                                TextInput::make('seo_keywords')
                                    ->label('SEO Keywords')
                                    ->maxLength(255),
                                Textarea::make('seo_description')
                                    ->label('SEO Description')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tab::make('Social Links')
                            ->schema([
                                TextInput::make('facebook_url')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('twitter_url')
                                    ->label('Twitter URL')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('youtube_url')
                                    ->label('YouTube URL')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('instagram_url')
                                    ->label('Instagram URL')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('linkedin_url')
                                    ->label('LinkedIn URL')
                                    ->url()
                                    ->maxLength(255),
                            ])->columns(2),

                        Tab::make('Contact Details')
                            ->schema([
                                TextInput::make('contact_email')
                                    ->label('Contact Email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('contact_phone')
                                    ->label('Contact Phone')
                                    ->tel()
                                    ->maxLength(255),
                                Textarea::make('contact_address')
                                    ->label('Contact Address')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
