<div class="max-h-screen p-4 grid md:grid-cols-3 gap-4">
    <!-- LEFT SECTION -->

    <div class="md:col-span-2 space-y-4">
        <div class="flex items-center gap-2">
            <flux:input type="text" wire:model.live="search" placeholder="Search..." class="input input-bordered w-full" />
        </div>

        <div class="flex flex-wrap gap-2">
            <!-- Kategori All -->
            <button wire:click="selectCategory(null)"
                class="badge px-4 py-2 rounded-full {{ $selectedCategory === null ? 'bg-orange-500 text-white' : 'border-1 border-orange-600' }}">
                Semua
            </button>

            <!-- Kategori Lain -->
            @foreach($categories as $category)
                <button wire:click="selectCategory({{ $category->id }})"
                    class="badge px-4 py-2 rounded-full {{ $selectedCategory === $category->id ? 'bg-orange-500 text-white' : 'border-1 border-orange-600' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>


       <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($products as $product)
            <div class="border p-4 rounded-xl shadow space-y-2">
                <div class="grid grid-cols-2 gap-2 items-start">
                    {{-- Gambar Produk --}}
                    <div>
                        <img
                            src="{{ asset('storage/' . ($product->images[0] ?? '')) }}"
                            alt="{{ $product->name }}"
                            class="w-full h-32 object-cover rounded"
                        >
                    </div>

                    {{-- Informasi Produk --}}
                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="font-semibold text-base text-gray-900">
                                {{ $product->name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                            </div>
                        </div>

                        {{-- Harga --}}
                        @if(isset($selectedPrice[$product->id]) && $selectedPrice[$product->id])
                            <div class="mt-3 text-sm font-semibold text-gray-800">
                                Rp {{ number_format($selectedPrice[$product->id], 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Variants --}}
                @if($product->variants->count() > 0)
                    <div class="space-y-2">
                        {{-- Pilih Ukuran --}}
                        <div class="text-sm font-medium">Pilih Ukuran:</div>
                            <div class="flex gap-2 flex-wrap">
                            @foreach($product->variants->pluck('size')->unique() as $size)
                                <button
                                    type="button"
                                    wire:click="$set('selectedSize.{{ $product->id }}', '{{ $size }}')"
                                    class="px-3 py-1 rounded border text-sm
                                        {{ ($selectedSize[$product->id] ?? null) === $size 
                                            ? 'bg-orange-500 text-white border-orange-600' 
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-orange-100' }}">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>


                        {{-- Pilih Suhu --}}
                        <div class="space-y-1">
                            <div class="text-sm font-medium">Pilih Suhu:</div>
                            <div class="flex gap-2 flex-wrap">
                                @foreach($product->variants->pluck('temperature')->unique() as $temp)
                                    <button
                                        type="button"
                                        wire:click="$set('selectedTemperature.{{ $product->id }}', '{{ $temp }}')"
                                        class="px-3 py-1 rounded border text-sm
                                            {{ ($selectedTemperature[$product->id] ?? null) === $temp 
                                                ? 'bg-orange-500 text-white border-orange-600' 
                                                : 'bg-white text-gray-700 border-gray-300 hover:bg-orange-100' }}">
                                        {{ $temp }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tombol Tambah ke Keranjang --}}
                        <flux:button
                            variant="primary"
                            icon="shopping-cart"
                            wire:click="addSelectedToCartDirect({{ $product->id }})"
                            class="btn btn-sm w-full bg-orange-500 hover:bg-orange-600 text-white mt-1">
                            Tambah ke Keranjang
                        </flux:button>
                    </div>
                @else
                    <div class="">
                        <img src="{{asset('images/empty-cart.svg')}}" alt="Empty Cart" class="w-24 h-24 mx-auto mb-2">
                    </div>
                    <div class="text-gray-500 text-sm">Tidak ada varian</div>
                @endif
            </div>
        @endforeach
    </div>
    </div>

    <div class="bg-white shadow p-4 rounded-lg h-full flex flex-col">
                   @if (session()->has('success'))
                <div x-data="{ visible: true }" x-show="visible" x-collapse class="py-4">
                    <div x-show="visible" x-transition>
                        <flux:callout icon="archive-box" variant="secondary" color="blue">
                            <flux:callout.heading>Status Pengaduan</flux:callout.heading>
                            <flux:callout.text>{{ session('success') }}.</flux:callout.text>
                
                            <x-slot name="controls">
                                <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                            </x-slot>
                        </flux:callout>
                    </div>
                </div>
            @endif

             @if (session()->has('error'))
                <div x-data="{ visible: true }" x-show="visible" x-collapse class="py-4">
                    <div x-show="visible" x-transition>
                        <flux:callout icon="archive-box" variant="secondary" color="blue">
                            <flux:callout.heading>Status Pengaduan</flux:callout.heading>
                            <flux:callout.text>{{ session('error') }}.</flux:callout.text>
                
                            <x-slot name="controls">
                                <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                            </x-slot>
                        </flux:callout>
                    </div>
                </div>
            @endif
        <div class="text-xl font-bold mb-4">Keranjang</div>

        <div class="flex-1 overflow-y-auto space-y-4">
            <label class="block text-base font-semibold text-gray-800 mb-2">Produk Order</label>
            @forelse($cart as $key => $item)
                <div class="flex gap-3 items-center justify-between">
                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/50' }}"
                        class="w-14 h-14 rounded object-cover" />

                    <div class="flex-1">
                        <div class="font-medium text-gray-800">{{ $item['name'] }}</div>
                        <div class="text-xs text-gray-500">{{ $item['size'] }} / {{ $item['temperature'] }}</div>
                        <div class="text-md text-gray-500">x {{ $item['qty'] }}</div>
                    </div>

                    <div class="text-sm font-semibold text-gray-800">
                        Rp{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                    </div>
                    <div class="flex gap-1 items-center">
                        <button wire:click="decreaseQty('{{ $key }}')" class="btn btn-xs">-</button>
                            <span class="px-2">{{ $item['qty'] }}</span>
                        <button wire:click="increaseQty('{{ $key }}')" class="btn btn-xs">+</button>
                    </div>
                </div>
            @empty
                <div class="">
                    <img src="{{asset('images/empty-cart.svg')}}" alt="Empty Cart" class="w-48 h-48 mx-auto mb-2">
                </div>
            @endforelse
        </div>

    <hr class="border-2 border-solid my-2">
    <div class="mt-2 text-sm text-gray-700">
        <div class="flex justify-between">
            <span>Subtotal</span>
            <span>Rp{{ number_format($this->getSubtotal(), 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2">
            <span>Tax (11%)</span>
            <span>Rp{{ number_format($this->getTax(), 0, ',', '.') }}</span>
        </div>
        <hr class="border-2 border-dashed my-2">
        <div class="flex justify-between font-bold text-base">
            <span>Total</span>
            <span>Rp{{ number_format($this->getTotal(), 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Order Type Section -->
    <div class="mt-6">
        <label class="block text-base font-semibold text-gray-800 mb-2">Order Type</label>
        <div class="flex gap-4">
            @php
                $orderTypes = [
                    ['label' => 'Dine In', 'value' => 'ditempat'],
                    ['label' => 'Take Away', 'value' => 'pulang'],
                ];
            @endphp

            @foreach($orderTypes as $type)
                <button
                    type="button"
                    wire:click="$set('orderType', '{{ $type['value'] }}')"
                    class="flex flex-col items-center px-4 py-3 rounded-xl border transition-all duration-200 w-full
                        {{ $orderType === $type['value'] 
                            ? 'bg-orange-100 border-orange-500 text-orange-700 font-semibold shadow' 
                            : 'bg-gray-100 border-gray-300 text-gray-400' }}">
                    <div class="text-sm mt-1">{{ $type['label'] }}</div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Payment Method Section -->
    <div class="mt-6">
        <label class="block text-base font-semibold text-gray-800 mb-2">Payment Method</label>
        <div class="flex gap-4">
            @php
                $paymentMethods = [
                    ['label' => 'Cash', 'value' => 'Cash'],
                    ['label' => 'E-Wallet', 'value' => 'Qris'],
                ];
            @endphp

            @foreach($paymentMethods as $method)
                <button
                    type="button"
                    wire:click="$set('paymentMethod', '{{ $method['value'] }}')"
                    class="flex flex-col items-center px-4 py-3 rounded-xl border transition-all duration-200 w-full
                        {{ $paymentMethod === $method['value'] 
                            ? 'bg-orange-100 border-orange-500 text-orange-700 font-semibold shadow' 
                            : 'bg-gray-100 border-gray-300 text-gray-400' }}">
                    <div class="text-sm mt-1">{{ $method['label'] }}</div>
                </button>
            @endforeach
        </div>
    </div>

        {{-- Place Order --}}
        <div class="mt-6">
            <button
                wire:click="placeOrder"
                wire:loading.attr="disabled"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded-lg transition"
            >
                <span wire:loading.remove>Buat Pesanan</span>
                <span wire:loading>Memproses...</span>
            </button>
        </div>
    </div>
</div>
