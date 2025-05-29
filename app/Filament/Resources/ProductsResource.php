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
use Filament\Forms\Components\Textarea;
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
    protected static ?int $navigationSort = 1;
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
                Textarea::make('description')  
                    ->label('Deskripsi Produk')
                    ->helperText('Deskripsi produk ini akan ditampilkan di halaman detail produk')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori Produk')
                    ->required(),
                FileUpload::make('images')
                    ->multiple()
                    ->label('Product Images')
                    ->disk('public') // atau 's3', tergantung konfigurasi Anda
                    ->directory('product-images')
                    ->imageEditor()
                    ->uploadingMessage('Uploading image...')
                    ->helperText('Maximum 5 gambar, ukuran maksimal 2MB per gambar')
                    ->maxFiles(5)
                    ->maxSize(2048) // 2MB
                    ->columnSpanFull(),

                Repeater::make('variants')
                    ->relationship('variants')
                    ->schema([
                        TextInput::make('size')
                            ->label('Ukuran')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Contoh: Kecil, Sedang, Besar, Normal'),
                        TextInput::make('temperature')
                            ->label('Suhu')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Contoh: Dingin, Hangat, Normal'),
                        TextInput::make('price')
                            ->numeric()
                            ->helperText('Masukan Harga Produk'),
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
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
