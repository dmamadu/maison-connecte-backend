<?php




namespace App\Filament\Resources;

use App\Filament\Resources\ProjectTypeResource\Pages;
use App\Models\ProjectType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectTypeResource extends Resource
{
    protected static ?string $model = ProjectType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Types de Projet';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Générales')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaNum()
                            ->helperText('Identifiant unique (ex: villa, apartment)')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name.fr')
                                    ->label('Nom (Français)')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('name.en')
                                    ->label('Nom (Anglais)')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('description.fr')
                                    ->label('Description (Français)')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description.en')
                                    ->label('Description (Anglais)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Apparence')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->label('Icône')
                                    ->helperText('Nom de l\'icône Heroicon (ex: heroicon-o-home)')
                                    ->placeholder('heroicon-o-home'),

                                Forms\Components\ColorPicker::make('color')
                                    ->label('Couleur')
                                    ->default('#3B82F6'),

                                Forms\Components\TextInput::make('order')
                                    ->label('Ordre')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Ordre d\'affichage'),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Afficher ce type dans le portfolio'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Couleur')
                    ->width(80),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('name.fr')
                    ->label('Nom (FR)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name.en')
                    ->label('Nom (EN)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('icon')
                    ->label('Icône')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Projets')
                    ->counts('projects')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
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
            ->defaultSort('order', 'asc')
            ->reorderable('order');
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
            'index' => Pages\ListProjectTypes::route('/'),
            'create' => Pages\CreateProjectType::route('/create'),
            'edit' => Pages\EditProjectType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}