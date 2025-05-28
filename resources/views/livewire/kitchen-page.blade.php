<div class="p-6 rounded-lg">
    <div wire:poll.live>
    <!-- Header dan Filter -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Kitchen Orders</h1>
            
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                <!-- Search -->
                    <input 
                        type="text" 
                        wire:model.live="search" 
                        placeholder="Cari order..." 
                        class="w-full pl-2 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                    >
                
                <!-- Order Type Filter -->
                <select 
                    wire:model.live="filterOrderType" 
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 "
                >
                    <option value="all">Semua Tipe</option>
                    <option value="ditempat">Dine In</option>
                    <option value="pulang">Take Away</option>
                </select>
            </div>
        </div>

        <!-- Flash Message -->
        @if (session('message'))
            <div class="mb-6 p-4 bg-green-100  border-l-4 border-green-500 text-green-700 rounded">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        <!-- Orders Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"> <!-- Changed from space-y-6 to grid -->
            @forelse ($orders as $order)
                <div class="border rounded-lg overflow-hidden shadow-sm">
                    <!-- Order Header -->
                    <div class=" px-4 py-3 flex justify-between items-center border-b">
                        <div>
                            <span class="font-semibold">Order #{{ $order->id }}</span>
                            <span class="ml-3 px-2 py-1 text-xs rounded-full 
                                {{ $order->order_type === 'ditempat' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $order->order_type === 'ditempat' ? 'Ditempat' : 'Pulang' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-200">
                            {{ $order->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="divide-y">
                        @foreach ($order->items as $item)
                            <div class="px-4 py-3 flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-gray-200 dark:text-gray-200 rounded-md overflow-hidden">
                                        @if($item->variant->product->images)
                                            <img 
                                                src="{{ asset('storage/'.$item->variant->product->images[0]) }}" 
                                                alt="{{ $item->variant->product->name }}"
                                                class="w-full h-full object-cover "
                                            >
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $item->variant->product->name }}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-200">
                                            {{ $item->variant->size }} • {{ $item->variant->temperature }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">X {{ $item->quantity }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Order Footer -->
                    <div class="px-4 py-3 flex justify-between items-center border-t">
                        <div class="text-sm">
                            <span class="font-medium">Customer:</span> 
                            <span>{{ $order->customer_name ?? 'Tanpa Nama' }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button 
                                wire:click="markAsDone({{ $order->id }})" 
                                class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition"
                            >
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12"> <!-- Added col-span-full -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada order</h3>
                    <p class="mt-1 text-gray-500">Semua order telah selesai diproses.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>