<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Catégories')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['fr'] ?? $record->slug)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('subcategory_id', null)),

                        Forms\Components\Select::make('subcategory_id')
                            ->label('Sous-catégorie')
                            ->options(function (callable $get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) {
                                    return [];
                                }
                                return SubCategory::where('category_id', $categoryId)
                                    ->get()
                                    ->pluck('name.fr', 'id');
                            })
                            ->searchable()
                            ->reactive(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Titre du produit')
                    ->description('Traduisez le titre dans les deux langues')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title.fr')
                                    ->label('Titre (Français)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Smartphone Galaxy S24'),

                                Forms\Components\TextInput::make('title.en')
                                    ->label('Titre (Anglais)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Galaxy S24 Smartphone'),
                            ]),
                    ]),

                Forms\Components\Section::make('Description')
                    ->description('Description détaillée du produit')
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\RichEditor::make('description.fr')
                                    ->label('Description (Français)')
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description.en')
                                    ->label('Description (Anglais)')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Forms\Components\Section::make('Prix et Image')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Prix')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required()
                            ->step(0.01),

                        Forms\Components\TextInput::make('link')
                            ->label('Lien externe')
                            ->url()
                            ->placeholder('https://example.com/product')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image')
                            ->label('Image principale')
                            ->image()
                            ->directory('products')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Points forts')
                    ->description('Liste des points forts du produit')
                    ->schema([
                        Forms\Components\Repeater::make('highlights')
                            ->label('Points forts')
                            ->schema([
                                Forms\Components\TextInput::make('text')
                                    ->label('Point fort')
                                    ->required()
                                    ->placeholder('Ex: Écran AMOLED 6.5 pouces')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un point fort')
                            ->reorderable(),
                    ]),

                Forms\Components\Section::make('Spécifications techniques')
                    ->description('Caractéristiques techniques du produit')
                    ->schema([
                        Forms\Components\Repeater::make('specs')
                            ->label('Spécifications')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Caractéristique')
                                    ->required()
                                    ->placeholder('Ex: Processeur'),

                                Forms\Components\TextInput::make('value')
                                    ->label('Valeur')
                                    ->required()
                                    ->placeholder('Ex: Snapdragon 8 Gen 3'),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une spécification')
                            ->reorderable(),
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
                    ->limit(40),

                Tables\Columns\TextColumn::make('title.en')
                    ->label('Titre (EN)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->limit(40),

                Tables\Columns\TextColumn::make('category.name.fr')
                    ->label('Catégorie')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subCategory.name.fr')
                    ->label('Sous-catégorie')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('XOF')
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

                Tables\Filters\SelectFilter::make('subcategory_id')
                    ->label('Sous-catégorie')
                    ->relationship('subCategory', 'slug')
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}