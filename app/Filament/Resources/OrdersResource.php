<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdersResource\Pages;
use App\Filament\Resources\OrdersResource\RelationManagers;
use App\Models\Order;
use App\Models\Orders;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersResource extends Resource
{
    protected static ?string $model = Order::class;

   protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $navigationGroup = 'Produk List';
    protected static ?string $navigationLabel = 'Order List';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('order_type')
                            ->label('Tipe Pemesanan')
                            ->options([
                                'ditempat' => 'Ditempat',
                                'pulang' => 'Pulang',
                            ])
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Cash' => 'Cash',
                                'Qris' => 'Qris',
                            ])
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Diproses' => 'Diproses',
                                'Selesai' => 'Selesai',
                            ])
                            ->required(),

                        Forms\Components\Select::make('pembayaran')
                            ->label('Status Pembayaran')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Sudah Dibayar' => 'Sudah Dibayar',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Harga')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Repeater::make('items')
                    ->label('Daftar Produk Dipesan')
                    ->relationship('items')
                    ->schema([
                        Forms\Components\Select::make('product_variant_id')
                            ->label('Varian Produk')
                            ->relationship('variant', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->product->name . ' - ' . $record->size . ' (' . $record->temperature . ')')
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')->label('Nama Pelanggan')->searchable(),
                TextColumn::make('order_type')
                    ->label('Tipe')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pulang' => 'primary',
                        'ditempat' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pulang' => 'heroicon-s-truck',
                        'ditempat' => 'heroicon-s-building-storefront',
                    })
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    }),
                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Cash' => 'primary',
                        'Qris' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Cash' => 'heroicon-s-banknotes',
                        'Qris' => 'heroicon-s-device-phone-mobile',
                    })
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'info',
                        'Diproses' => 'warning',
                        'Selesai' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Menunggu' => 'heroicon-s-clock',
                        'Diproses' => 'heroicon-s-arrow-path',
                        'Selesai' => 'heroicon-s-check-badge',
                    })
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    }),
                TextColumn::make('pembayaran')
                    ->label('Pembayaran')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                            'Menunggu' => 'danger',
                            'Sudah Dibayar' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                            'Menunggu' => 'heroicon-s-clock',
                            'Sudah Dibayar' => 'heroicon-s-banknotes',
                    }),
                TextColumn::make('total_price')->money('IDR')->label('Total'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Waktu Pemesanan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}/view'),
        ];
    }
}
