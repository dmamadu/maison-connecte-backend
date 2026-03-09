<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom du client')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => 
                                        $set('slug', \Illuminate\Support\Str::slug($state))
                                    ),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('clients')
                            ->required()
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('website')
                                    ->label('Site web')
                                    ->url()
                                    ->placeholder('https://example.com'),

                                Forms\Components\Select::make('industry')
                                    ->label('Secteur d\'activité')
                                    ->options([
                                        'Technology' => 'Technologie',
                                        'Construction' => 'Construction',
                                        'Real Estate' => 'Immobilier',
                                        'Hospitality' => 'Hôtellerie',
                                        'Healthcare' => 'Santé',
                                        'Education' => 'Éducation',
                                        'Finance' => 'Finance',
                                        'Retail' => 'Commerce',
                                        'Manufacturing' => 'Industrie',
                                        'Energy' => 'Énergie',
                                        'Other' => 'Autre',
                                    ])
                                    ->searchable(),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->placeholder('Courte description du client...')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Coordonnées')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('contact_person')
                                    ->label('Personne de contact')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->label('Adresse')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('city')
                                    ->label('Ville')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('country')
                                    ->label('Pays')
                                    ->default('Sénégal')
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Paramètres')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('partnership_start')
                                    ->label('Début du partenariat')
                                    ->default(now()),

                                Forms\Components\TextInput::make('order')
                                    ->label('Ordre d\'affichage')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Plus petit = affiché en premier'),

                                Forms\Components\Placeholder::make(''),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Client mis en avant')
                                    ->default(false)
                                    ->helperText('Afficher ce client en priorité'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Actif')
                                    ->default(true)
                                    ->helperText('Afficher ce client sur le site'),
                            ]),
                    ]),
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

                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->square()
                    ->size(50),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('industry')
                    ->label('Secteur')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('partnership_start')
                    ->label('Partenariat depuis')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('★')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable()
                    ->tooltip('Client mis en avant'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mis en avant')
                    ->placeholder('Tous')
                    ->trueLabel('Mis en avant')
                    ->falseLabel('Standards'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),

                Tables\Filters\SelectFilter::make('industry')
                    ->label('Secteur')
                    ->options([
                        'Technology' => 'Technologie',
                        'Construction' => 'Construction',
                        'Real Estate' => 'Immobilier',
                        'Hospitality' => 'Hôtellerie',
                        'Healthcare' => 'Santé',
                        'Education' => 'Éducation',
                        'Finance' => 'Finance',
                        'Retail' => 'Commerce',
                        'Manufacturing' => 'Industrie',
                        'Energy' => 'Énergie',
                    ])
                    ->multiple(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            // 'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}