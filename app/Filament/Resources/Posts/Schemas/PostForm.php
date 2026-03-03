<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Core Settings')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Enter unique article slug'),

                        Select::make('categories')
                            ->label('Categories')
                            ->relationship(
                                name: 'categories', 
                                titleAttribute: 'slug',
                                modifyQueryUsing: fn (Builder $query) => $query->with('translation')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->translation?->name ?? $record->slug)
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->required(),

                        Select::make('author_id')
                            ->label('Author')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn() => \Illuminate\Support\Facades\Auth::id()),

                        FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->directory('posts')
                            ->columnSpanFull(),
                    ])->columns(2),

                Tabs::make('Content & Translations')
                    ->tabs([
                        Tab::make('Bangla (Primary)')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('title_bn')
                                    ->label('Article Title (Bangla)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                Textarea::make('excerpt_bn')
                                    ->label('Short Excerpt')
                                    ->rows(3),
                                RichEditor::make('content_bn')
                                    ->label('Full Content (Bangla)')
                                    ->required()
                                    ->fileAttachmentsDirectory('post-attachments'),
                            ]),
                        Tab::make('English')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('Article Title (English)'),
                                Textarea::make('excerpt_en')
                                    ->label('Short Excerpt')
                                    ->rows(3),
                                RichEditor::make('content_en')
                                    ->label('Full Content (English)')
                                    ->fileAttachmentsDirectory('post-attachments'),
                            ]),
                        Tab::make('SEO & Meta')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                TextInput::make('meta_title_bn')->label('Meta Title (BN)'),
                                Textarea::make('meta_description_bn')->label('Meta Description (BN)'),
                                TextInput::make('meta_title_en')->label('Meta Title (EN)'),
                                Textarea::make('meta_description_en')->label('Meta Description (EN)'),
                            ]),
                    ])->columnSpanFull(),

                Section::make('Publishing & Visibility')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->native(false)
                            ->default('draft')
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label('Publication Date'),

                        Toggle::make('is_featured')
                            ->label('Featured Post'),
                        Toggle::make('is_breaking')
                            ->label('Breaking News'),
                    ])->columns(2),
            ]);
    }
}
