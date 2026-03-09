<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubCategoryResource\Pages;
use App\Models\SubCategory;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int $navigationSort = 2;

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
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaNum()
                            ->helperText('Identifiant unique (ex: smartphones, laptops)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Nom de la sous-catégorie')
                    ->description('Traduisez le nom dans les deux langues')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name.fr')
                                    ->label('Nom (Français)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Smartphones'),

                                Forms\Components\TextInput::make('name.en')
                                    ->label('Nom (Anglais)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Smartphones'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name.fr')
                    ->label('Catégorie')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->icon('heroicon-o-link'),

                Tables\Columns\TextColumn::make('name.fr')
                    ->label('Nom (FR)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('name.en')
                    ->label('Nom (EN)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubCategories::route('/'),
            'create' => Pages\CreateSubCategory::route('/create'),
            'edit'   => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}