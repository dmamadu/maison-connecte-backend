<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['fr'] ?? $record->slug)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('author')
                                    ->label('Auteur')
                                    ->maxLength(255)
                                    ->placeholder('Ex: John Doe'),

                                Forms\Components\TextInput::make('read_time')
                                    ->label('Temps de lecture')
                                    ->placeholder('Ex: 5 min')
                                    ->maxLength(50),
                            ]),

                        Forms\Components\DatePicker::make('published_at')
                            ->label('Date de publication')
                            ->required()
                            ->default(now()),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Titre de l\'article')
                    ->description('Traduisez le titre dans les deux langues')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title.fr')
                                    ->label('Titre (Français)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Guide complet de la domotique'),

                                Forms\Components\TextInput::make('title.en')
                                    ->label('Titre (Anglais)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Complete guide to home automation'),
                            ]),
                    ]),

                Forms\Components\Section::make('Extrait / Résumé')
                    ->description('Court résumé de l\'article dans les deux langues')
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Textarea::make('excerpt.fr')
                                    ->label('Extrait (Français)')
                                    ->rows(4)
                                    ->placeholder('Résumé en français...')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('excerpt.en')
                                    ->label('Extrait (Anglais)')
                                    ->rows(4)
                                    ->placeholder('Summary in English...')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Forms\Components\Section::make('Image de couverture')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->directory('blog')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->size(60),

                Tables\Columns\TextColumn::make('title.fr')
                    ->label('Titre (FR)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('title.en')
                    ->label('Titre (EN)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category.name.fr')
                    ->label('Catégorie')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Auteur')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('read_time')
                    ->label('Temps de lecture')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié le')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['fr'] ?? $record->slug)
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}