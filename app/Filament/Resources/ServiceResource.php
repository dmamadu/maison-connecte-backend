<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\ServiceResource\Pages;
// use App\Models\Service;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;

// class ServiceResource extends Resource
// {
//     protected static ?string $model = Service::class;

//     protected static ?string $navigationIcon = 'heroicon-o-wrench';
//     protected static ?string $navigationGroup = 'Catalogue';
//     protected static ?string $navigationLabel = 'Services';
//     protected static ?int $navigationSort = 2;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
                
//                 Forms\Components\Section::make('Informations du service')
//                     ->schema([
//                         Forms\Components\TextInput::make('name')
//                             ->label("Nom du service")
//                             ->required()
//                             ->maxLength(255),

//                         Forms\Components\Textarea::make('description')
//                             ->label("Description")
//                             ->required()
//                             ->rows(4),

//                         Forms\Components\TextInput::make('price')
//                             ->label("Prix")
//                             ->numeric()
//                             ->nullable(),

//                         Forms\Components\Toggle::make('available_online')
//                             ->label("Disponible en ligne")
//                             ->default(true)
//                             ->inline(false),
//                     ])
//                     ->columns(2),

//                 Forms\Components\Section::make('Image du service')
//                     ->schema([
//                         Forms\Components\FileUpload::make('image')
//                             ->label('Image')
//                             ->directory('service_images')
//                             ->image()
//                             ->visibility('public')
//                             ->maxSize(2048)
//                             ->imageEditor() // recadrage automatique
//                             ->previewable()
//                             ->downloadable(),
//                     ])
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
                
//                 Tables\Columns\ImageColumn::make('image')
//                     ->label('Image')
//                     ->square()
//                     ->size(50),

//                 Tables\Columns\TextColumn::make('name')
//                     ->label('Nom')
//                     ->searchable()
//                     ->sortable(),

//                 Tables\Columns\TextColumn::make('price')
//                     ->label('Prix')
//                     ->money('XOF')
//                     ->sortable(),

//                 Tables\Columns\IconColumn::make('available_online')
//                     ->label('En ligne')
//                     ->boolean(),
//             ])
//             ->filters([])
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\DeleteAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\DeleteBulkAction::make(),
//             ])
//             ->defaultSort('id', 'desc');
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index'  => Pages\ListServices::route('/'),
//             'create' => Pages\CreateService::route('/create'),
//             'edit'   => Pages\EditService::route('/{record}/edit'),
//         ];
//     }
// }
