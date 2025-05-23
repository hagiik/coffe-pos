<x-filament::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Detail Pesanan
            </x-slot>
            
            <div>
                <p><strong>Nama Pelanggan:</strong> {{ $record->customer_name }}</p>
                <p><strong>Status:</strong> {{ $record->status }}</p>
                <p><strong>Pembayaran:</strong> {{ $record->pembayaran }}</p>
                <p><strong>Metode:</strong> {{ $record->payment_method }}</p>
                <p><strong>Total:</strong> Rp{{ number_format($record->total_price, 0, ',', '.') }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Item Produk
            </x-slot>

            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Produk</th>
                        <th scope="col" class="px-6 py-3">Ukuran</th>
                        <th scope="col" class="px-6 py-3">Suhu</th>
                        <th scope="col" class="px-6 py-3">Jumlah</th>
                        <th scope="col" class="px-6 py-3">Harga Satuan</th>
                        <th scope="col" class="px-6 py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->items as $item)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4">{{ $item->variant->product->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->variant->size }}</td>
                            <td class="px-6 py-4">{{ $item->variant->temperature }}</td>
                            <td class="px-6 py-4">{{ $item->quantity }}</td>
                            <td class="px-6 py-4">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>
    </div>
</x-filament::page>
