<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaNum()
                            ->helperText('Identifiant unique (ex: electronics, furniture)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Nom de la catégorie')
                    ->description('Traduisez le nom dans les deux langues')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name.fr')
                                    ->label('Nom (Français)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Électronique'),

                                Forms\Components\TextInput::make('name.en')
                                    ->label('Nom (Anglais)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Electronics'),
                            ]),
                    ]),

                Forms\Components\Section::make('Description')
                    ->description('Traduisez la description dans les deux langues')
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Textarea::make('description.fr')
                                    ->label('Description (Français)')
                                    ->rows(4)
                                    ->placeholder('Description en français...')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description.en')
                                    ->label('Description (Anglais)')
                                    ->rows(4)
                                    ->placeholder('Description en anglais...')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Slug copié!')
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

                Tables\Columns\TextColumn::make('description.fr')
                    ->label('Description (FR)')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('description.en')
                    ->label('Description (EN)')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                //
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}