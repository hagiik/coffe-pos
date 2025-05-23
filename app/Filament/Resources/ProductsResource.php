<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Filament\Resources\ProductsResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductsResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Produk List';
    protected static ?string $navigationLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->label('Nama Produk')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Slug akan terisi otomatis setelah mengisi nama poruduk')
                    ->unique(Product::class, 'slug', ignoreRecord: true),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori Produk')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('images')
                    ->multiple()
                    ->label('Product Images')
                    ->disk('public') // atau 's3', tergantung konfigurasi Anda
                    ->directory('product-images')
                    ->imageEditor()
                    ->uploadingMessage('Uploading image...')
                    ->helperText('Upload multiple product images')
                    ->columnSpanFull(),

                Repeater::make('variants')
                    ->relationship('variants')
                    ->schema([
                        Select::make('size')->options([
                            'Kecil' => 'Kecil',
                            'Sedang' => 'Sedang',
                            'Besar' => 'Besar',
                            'Normal' => 'Normal',
                        ]),
                        Select::make('temperature')->options([
                            'Dingin' => 'Dingin',
                            'Hangat' => 'Hangat',
                            'Normal' => 'Normal',
                        ]),
                        TextInput::make('price')->numeric(),
                    ])
                    ->label('Varian Produk')
                    // ->minItems(1)
                    ->columns(3)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug'),

                TextColumn::make('category.name')
                    ->label('Kategori'),

                ImageColumn::make('images')
                    ->label('Foto')
                    ->limit(1) // Tampilkan 1 gambar pertama
                    ->circular(), // opsional styling

                ViewColumn::make('variants')
                    ->label('Varian')
                    ->view('filament.columns.product-variants'), // custom blade view
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
            'view' => Pages\ViewProducts::route('/{record}/view'),
        ];
    }
}
