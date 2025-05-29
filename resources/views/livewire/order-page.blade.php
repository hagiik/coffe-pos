<div class="max-h-screen p-2 md:p-4 grid grid-cols-1 sm:grid-col-2 md:grid-cols-3 gap-2 md:gap-4 bg-white dark:bg-zinc-800">
    <!-- LEFT SECTION (Products) -->
    <div class="md:col-span-2 space-y-2 md:space-y-4 overflow-y-auto h-[calc(100vh-8rem)] md:h-screen md:sticky md:top-0">
        <!-- Search -->
        <div class="flex items-center gap-2 sticky top-0 bg-white dark:bg-zinc-800 z-10 p-2">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="Search..." 
                class="input w-full p-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
            />
        </div>

        <!-- Categories -->
        <div class="flex overflow-x-auto gap-2 pb-2 sticky top-16 bg-white dark:bg-zinc-800 z-10 p-2">
            <!-- All Category -->
            <button wire:click="selectCategory(null)"
                class="whitespace-nowrap px-3 py-1 rounded-full text-sm {{ $selectedCategory === null ? 'bg-orange-500 text-white' : 'border border-orange-600 text-orange-600 dark:text-orange-300' }}">
                Semua
            </button>

            <!-- Other Categories -->
            @foreach($categories as $category)
                <button wire:click="selectCategory({{ $category->id }})"
                    class="whitespace-nowrap px-3 py-1 rounded-full text-sm {{ $selectedCategory === $category->id ? 'bg-orange-500 text-white' : 'border border-orange-600 text-orange-600 dark:text-orange-300' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-2 md:gap-4 pb-4">
            @foreach($products as $product)
                <div class="border dark:border-orange-600 p-3 rounded-xl shadow space-y-2">
                    <div class="grid grid-cols-2 gap-2 items-start">
                        <!-- Product Image -->
                        <div>
                            <img
                                src="{{ asset( ($product->images[0] ?? './images/empty-cart.svg')) }}"
                                alt="{{ $product->name ?? 'Product Image' }}"
                                class="w-full h-24 md:h-32 object-cover rounded"
                                loading="lazy"
                            >
                        </div>

                        <!-- Product Info -->
                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <div class="font-semibold text-sm md:text-base text-gray-900 dark:text-gray-200 line-clamp-2">
                                    {{ $product->name }}
                                </div>
                                <div class="text-xs md:text-sm text-gray-600 dark:text-gray-200 line-clamp-2">
                                    {{ $product->description }}
                                </div>
                            </div>

                            <!-- Price -->
                            @if(isset($selectedPrice[$product->id]) && $selectedPrice[$product->id])
                                <div class="mt-1 md:mt-3 text-xs md:text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    Rp {{ number_format($selectedPrice[$product->id], 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Variants -->
                    @if($product->variants->count() > 0)
                        <div class="space-y-2">
                            <!-- Size Selection -->
                            <div class="text-xs md:text-sm font-medium">Pilih Ukuran:</div>
                            <div class="flex gap-1 flex-wrap">
                                @foreach($product->variants->pluck('size')->unique() as $size)
                                    <button
                                        type="button"
                                        wire:click="$set('selectedSize.{{ $product->id }}', '{{ $size }}')"
                                        class="px-2 py-1 rounded border text-xs md:text-sm
                                            {{ ($selectedSize[$product->id] ?? null) === $size 
                                                ? 'bg-orange-500 text-white border-orange-600' 
                                                : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 hover:bg-orange-100 dark:hover:bg-orange-900' }}">
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- Temperature Selection -->
                            @if($selectedSize[$product->id] ?? false)
                                <div class="space-y-1">
                                    <div class="text-xs md:text-sm font-medium">Pilih Variant:</div>
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach($this->getTemperaturesBySize($product->id, $selectedSize[$product->id]) as $temp)
                                            <button
                                                type="button"
                                                wire:click="$set('selectedTemperature.{{ $product->id }}', '{{ $temp }}')"
                                                class="px-2 py-1 rounded border text-xs md:text-sm
                                                    {{ ($selectedTemperature[$product->id] ?? null) === $temp 
                                                        ? 'bg-orange-500 text-white border-orange-600' 
                                                        : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 hover:bg-orange-100 dark:hover:bg-orange-900' }}">
                                                {{ $temp }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Add to Cart Button -->
                            <button
                                wire:click="addSelectedToCartDirect({{ $product->id }})"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm md:text-base py-1 md:py-2 rounded-md mt-1 flex items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Tambah ke Keranjang
                            </button>
                        </div>
                    @else
                        <div class="text-center">
                            <img src="{{asset('images/empty-cart.svg')}}" alt="Empty Cart" class="w-16 h-16 mx-auto mb-2">
                            <div class="text-gray-500 text-xs md:text-sm">Tidak ada varian</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT SECTION (Cart) -->
    <div class="md:col-span-1 md:sticky md:mt-2">
        <div class="shadow p-3 md:p-4 rounded-lg bg-white dark:bg-zinc-800 min-h-[calc(100vh-8rem)] md:min-h-screen md:h-screen overflow-y-auto md:sticky md:top-0 flex flex-col">
            @if (session()->has('success'))
                <div x-data="{ visible: true }" x-show="visible" class="mb-2 p-3 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <div class="font-semibold">Status Pesanan</div>
                                <div class="text-sm">{{ session('success') }}</div>
                            </div>
                        </div>
                        <button x-on:click="visible = false" class="text-blue-500 hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ visible: true }" x-show="visible" class="mb-2 p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <div class="font-semibold">Status Pesanan</div>
                                <div class="text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button x-on:click="visible = false" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
            
            <div class="text-lg md:text-xl font-bold mb-2 md:mb-4 text-gray-800 dark:text-gray-200">Keranjang</div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto space-y-2">
                <label class="block text-sm md:text-base font-semibold text-gray-800 dark:text-gray-200 mb-1 md:mb-2">Produk Order</label>
                @forelse($cart as $key => $item)
                    <div class="flex gap-2 md:gap-3 items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <img src="{{ $item['image'] ? asset( $item['image']) : './images/empty-cart.svg' }}"
                            class="w-10 h-10 md:w-14 md:h-14 rounded object-cover" />
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm md:text-base text-gray-800 dark:text-gray-200 truncate">{{ $item['name'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-300">{{ $item['size'] }} / {{ $item['temperature'] }}</div>
                            <div class="text-xs md:text-sm text-gray-500 dark:text-gray-300">x {{ $item['qty'] }}</div>
                        </div>

                        <div class="text-xs md:text-sm font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            Rp{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                        </div>
                        <div class="flex gap-1 items-center">
                            <button wire:click="decreaseQty('{{ $key }}')" class="btn btn-xs">-</button>
                                <span class="px-2">{{ $item['qty'] }}</span>
                            <button wire:click="increaseQty('{{ $key }}')" class="btn btn-xs">+</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 flex-1 flex flex-col items-center justify-center">
                        <img src="{{asset('images/empty-cart.svg')}}" alt="Empty Cart" class="w-48 h-48 mx-auto mb-2">
                        <div class="text-gray-500 dark:text-gray-400">Keranjang kosong</div>
                    </div>
                @endforelse
            </div>

            <!-- Summary -->
            <hr class="border my-2 dark:border-gray-600">
            <div class="mt-2 text-xs md:text-sm text-gray-700 dark:text-gray-200 space-y-1">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp{{ number_format($this->getSubtotal(), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax (11%)</span>
                    <span>Rp{{ number_format($this->getTax(), 0, ',', '.') }}</span>
                </div>
                <hr class="border border-dashed my-1 dark:border-gray-600">
                <div class="flex justify-between font-bold text-sm md:text-base">
                    <span>Total</span>
                    <span>Rp{{ number_format($this->getTotal(), 0, ',', '.') }}</span>
                </div>
            </div>

            <hr class="border my-2 dark:border-gray-600">
            <div class="mt-4">
                <flux:label for="customername" badge="Optional" class="block text-sm md:text-base font-semibold text-gray-800 dark:text-gray-200 mb-1 md:mb-2">Nama Pelanggan</flux:label>
                <flux:input type="text" id="customername" wire:model.defer="customername"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Masukkan nama pelanggan atau kosongkan" />
            </div>

            <!-- Order Type Section -->
            <div class="mt-4">
                <label class="block text-sm md:text-base font-semibold text-gray-800 dark:text-gray-200 mb-1 md:mb-2">Order Type</label>
                <div class="grid grid-cols-2 gap-2">
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
                            class="px-2 py-2 md:px-4 md:py-3 rounded-lg border transition-all duration-200
                                {{ $orderType === $type['value'] 
                                    ? 'bg-orange-100 dark:bg-orange-900 border-orange-500 text-orange-700 dark:text-orange-200 font-semibold shadow' 
                                    : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-300' }}">
                            <div class="text-xs md:text-sm">{{ $type['label'] }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Payment Method Section -->
            <div class="mt-4">
                <label class="block text-sm md:text-base font-semibold text-gray-800 dark:text-gray-200 mb-1 md:mb-2">Payment Method</label>
                <div class="grid grid-cols-2 gap-2">
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
                            class="px-2 py-2 md:px-4 md:py-3 rounded-lg border transition-all duration-200
                                {{ $paymentMethod === $method['value'] 
                                    ? 'bg-orange-100 dark:bg-orange-900 border-orange-500 text-orange-700 dark:text-orange-200 font-semibold shadow' 
                                    : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-300' }}">
                            <div class="text-xs md:text-sm">{{ $method['label'] }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Place Order Button -->
            <div class="mt-4">
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
</div>