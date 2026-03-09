<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\PackResource\Pages;
// use App\Models\Pack;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Columns\BooleanColumn;

// class PackResource extends Resource
// {
//     protected static ?string $model = Pack::class;

//     protected static ?string $navigationIcon = 'heroicon-o-tag';
//     protected static ?string $navigationGroup = 'Catalogue';
//     protected static ?int $navigationSort = 2;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\TextInput::make('name')
//                     ->label('Nom')
//                     ->required()
//                     ->maxLength(255),

//                 Forms\Components\Textarea::make('description')
//                     ->label('Description')
//                     ->rows(3)
//                     ->nullable(),

//                 Forms\Components\TextInput::make('price')
//                     ->label('Prix')
//                     ->numeric()
//                     ->required(),

//                 Forms\Components\Toggle::make('installation_included')
//                     ->label('Installation incluse'),

//                 Forms\Components\MultiSelect::make('products')
//                     ->relationship('products', 'title')
//                     ->preload()
//                     ->label('Produits du pack'),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 TextColumn::make('name')->sortable()->searchable(),
//                 TextColumn::make('price')->sortable(),
//                 BooleanColumn::make('installation_included')
//                     ->label('Installation incluse'),
//             ])
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\DeleteAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\DeleteBulkAction::make(),
//             ]);
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index'  => Pages\ListPacks::route('/'),
//             'create' => Pages\CreatePack::route('/create'),
//             'edit'   => Pages\EditPack::route('/{record}/edit'),
//         ];
//     }
// }
