<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\AccessoryResource\Pages;
// use App\Models\Accessory;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;

// class AccessoryResource extends Resource
// {
//     protected static ?string $model = Accessory::class;

//     protected static ?string $navigationIcon = 'heroicon-o-tag';
//     protected static ?string $navigationGroup = 'Catalogue';
//     protected static ?string $navigationLabel = 'Accessoires';
//     protected static ?int $navigationSort = 3;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([

//                 Forms\Components\Section::make('Informations')
//                     ->schema([
//                         Forms\Components\TextInput::make('title')
//                             ->label('Titre')
//                             ->required(),

//                         Forms\Components\Textarea::make('description')
//                             ->label('Description')
//                             ->rows(4),

//                         Forms\Components\TextInput::make('price')
//                             ->label('Prix')
//                             ->numeric()
//                             ->nullable(),
//                     ])
//                     ->columns(2),

//                 Forms\Components\Section::make('Image')
//                     ->schema([
//                         Forms\Components\FileUpload::make('image')
//                             ->directory('accessories')
//                             ->image()
//                             ->visibility('public')
//                             ->maxSize(2048)
//                             ->imageEditor()
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
//                     ->size(60)
//                     ->square(),

//                 Tables\Columns\TextColumn::make('title')
//                     ->searchable()
//                     ->sortable(),

//                 Tables\Columns\TextColumn::make('price')
//                     ->money('XOF')
//                     ->sortable(),
//             ])
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
//             'index' => Pages\ListAccessories::route('/'),
//             'create' => Pages\CreateAccessory::route('/create'),
//             'edit' => Pages\EditAccessory::route('/{record}/edit'),
//         ];
//     }
// }
