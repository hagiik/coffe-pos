<x-layouts.app :title="__('Order')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden">
            @livewire('order-page')
        </div>
    </div>
</x-layouts.app>
