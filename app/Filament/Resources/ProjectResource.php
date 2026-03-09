<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use App\Models\ProjectType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Projets';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du Projet')
                    ->schema([
                        Forms\Components\Select::make('project_type_id')
                            ->label('Type de Projet')
                            ->relationship('projectType', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['fr'] ?? $record->slug)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique()
                                    ->alphaNum(),
                                Forms\Components\TextInput::make('name.fr')
                                    ->label('Nom (FR)')
                                    ->required(),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title.fr')
                                    ->label('Titre (Français)')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('title.en')
                                    ->label('Titre (Anglais)')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\RichEditor::make('description.fr')
                                    ->label('Description (Français)')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description.en')
                                    ->label('Description (Anglais)')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Détails')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('location')
                                    ->label('Localisation')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Almadies, Dakar'),

                                Forms\Components\TextInput::make('year')
                                    ->label('Année')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(4)
                                    ->placeholder('2024'),

                                Forms\Components\TextInput::make('duration')
                                    ->label('Durée')
                                    ->maxLength(255)
                                    ->placeholder('Ex: 6 mois'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('client')
                                    ->label('Client')
                                    ->maxLength(255)
                                    ->placeholder('Ex: Privé'),

                                Forms\Components\TextInput::make('order')
                                    ->label('Ordre d\'affichage')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Projet actif')
                            ->default(true)
                            ->helperText('Afficher ce projet dans le portfolio'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Images')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Image Principale')
                            ->image()
                            ->directory('projects/thumbnails')
                            ->required()
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('images')
                            ->label('Galerie d\'Images')
                            ->relationship('images')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Image')
                                    ->image()
                                    ->directory('projects/images')
                                    ->required()
                                    ->maxSize(2048)
                                    ->imageEditor(),

                                Forms\Components\TextInput::make('order')
                                    ->label('Ordre')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['order'] ?? null)
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une image'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Tags & Services')
                    ->schema([
                        Forms\Components\Repeater::make('tags')
                            ->label('Tags')
                            ->relationship('tags')
                            ->schema([
                                Forms\Components\TextInput::make('tag')
                                    ->label('Tag')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Ex: Sécurité, Domotique')
                                    ->columnSpanFull(),
                            ])
                            ->reorderable(false)
                            ->collapsible()
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un tag'),

                        Forms\Components\Repeater::make('services')
                            ->label('Services')
                            ->relationship('services')
                            ->schema([
                                Forms\Components\Select::make('service')
                                    ->label('Service')
                                    ->options([
                                        'security' => 'Sécurité',
                                        'automation' => 'Domotique',
                                        'solar' => 'Solaire',
                                        'finishing' => 'Finitions',
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->reorderable(false)
                            ->collapsible()
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un service'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Image')
                    ->circular()
                    ->width(60)
                    ->height(60),

                Tables\Columns\TextColumn::make('title.fr')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('projectType.name.fr')
                    ->label('Type')
                    ->badge()
                    ->color(fn (Project $record): string => $record->projectType->color ?? 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Localisation')
                    ->icon('heroicon-o-map-pin')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Année')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('client')
                    ->label('Client')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('images_count')
                    ->label('Images')
                    ->counts('images')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('tags_count')
                    ->label('Tags')
                    ->counts('tags')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable()
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
                Tables\Filters\SelectFilter::make('project_type_id')
                    ->label('Type de Projet')
                    ->relationship('projectType', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['fr'] ?? $record->slug)
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),

                Tables\Filters\Filter::make('year')
                    ->form([
                        Forms\Components\TextInput::make('year')
                            ->label('Année')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['year'],
                            fn (Builder $query, $year): Builder => $query->where('year', $year),
                        );
                    }),
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
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}