@php
    $variants = $getRecord()->variants;
@endphp

<div class="space-y-1">
    @foreach ($variants as $variant)
        <div class="text-sm">
            <span class="font-semibold">{{ $variant->size }}</span>
            /
            <span>{{ $variant->temperature }}</span>
            —
            <span class="text-green-600">Rp{{ number_format($variant->price, 0, ',', '.') }}</span>
        </div>
    @endforeach
</div>
