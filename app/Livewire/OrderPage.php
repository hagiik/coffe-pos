<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\product_variants;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrderPage extends Component
{
    public $search = '';
    public $selectedCategory = null;
    public $selectedPrice = [];
    public $showModal = false;
    public $selectedProduct;
    public $selectedSize = [];
    public $selectedTemperature = [];
    public $customername = '';
    public $cart = [];
    public $orderType = 'ditempat'; 
    public $paymentMethod = 'Cash';

    public function render()
    {
        $categories = Category::all();
        $products = Product::with('variants')
            ->when($this->selectedCategory, fn ($q) => $q->where('category_id', $this->selectedCategory))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get();

        foreach ($products as $product) {
            if (!isset($this->selectedSize[$product->id])) {
                $this->selectedSize[$product->id] = $product->variants->first()->size ?? null;
            }
            if (!isset($this->selectedTemperature[$product->id])) {
                $this->selectedTemperature[$product->id] = $product->variants->first()->temperature ?? null;
            }
            if (!isset($this->selectedPrice[$product->id])) {
                $this->selectedPrice[$product->id] = $product->variants->first()->price ?? null;
            }
        }

        return view('livewire.order-page', compact('products', 'categories'));
    }

    public function openModal($productId)
    {
        $this->selectedProduct = Product::with('variants')->findOrFail($productId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduct = null;
    }

    public function addSelectedToCartDirect($productId)
    {
        $size = $this->selectedSize[$productId] ?? null;
        $temperature = $this->selectedTemperature[$productId] ?? null;

        if (!$size || !$temperature) {
            return;
        }

        $variant = product_variants::where('product_id', $productId)
            ->where('size', $size)
            ->where('temperature', $temperature)
            ->first();

        if ($variant) {
            $this->addToCart($variant->id);
            unset($this->selectedSize[$productId], $this->selectedTemperature[$productId]);
            $this->closeModal();
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function updatedSelectedSize($value, $key)
    {
        $this->updateVariantPrice($key);
        $this->selectedTemperature[$key] = null;
    }

    public function updatedSelectedTemperature($value, $key)
    {
        $this->updateVariantPrice($key);
    }

    public function getTemperaturesBySize($productId, $size)
    {
        if (!$size) return collect();

        return product_variants::where('product_id', $productId)
            ->where('size', $size)
            ->pluck('temperature')
            ->unique();
    }
    public function updateVariantPrice($productId)
    {
        $size = $this->selectedSize[$productId] ?? null;
        $temperature = $this->selectedTemperature[$productId] ?? null;

        if (!$size) {
            $this->selectedPrice[$productId] = null;
            $this->selectedTemperature[$productId] = null;
            return;
        }

        // Jika temperature yang dipilih tidak tersedia untuk size yang dipilih, reset temperature
        $availableTemps = $this->getTemperaturesBySize($productId, $size);
        if ($temperature && !$availableTemps->contains($temperature)) {
            $this->selectedTemperature[$productId] = null;
            $temperature = null;
        }

        $variant = product_variants::where('product_id', $productId)
            ->where('size', $size)
            ->when($temperature, fn($q) => $q->where('temperature', $temperature))
            ->first();

        $this->selectedPrice[$productId] = $variant?->price;
    }

    public function getSubtotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });
    }

    public function getTax()
    {
        // Pajak dihitung berdasarkan subtotal
        return $this->getSubtotal() * 0.11;
    }

    public function getTotal()
    {
        // Total adalah subtotal ditambah pajak
        return $this->getSubtotal() + $this->getTax();
    }

    public function addToCart($variantId, $qty = 1)
    {
        $variant = product_variants::with('product')->findOrFail($variantId);
        $key = $variantId;

        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty'] += $qty;
        } else {
            $this->cart[$key] = [
                'id' => $variantId,
                'name' => $variant->product->name,
                'size' => $variant->size,
                'temperature' => $variant->temperature,
                'price' => $variant->price,
                'image' => $variant->product->images[0] ?? null,
                'qty' => $qty,
            ];
        }
    }

    public function increaseQty($key)
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty']++;
        }
    }

    public function decreaseQty($key)
    {
        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['qty'] > 1) {
                $this->cart[$key]['qty']--;
            } else {
                unset($this->cart[$key]);
            }
        }
    }

    public function placeOrder()
    {
        $this->validate([
            'paymentMethod' => 'required|in:Cash,Qris',
            'orderType' => 'required|in:ditempat,pulang',
            'cart' => 'required|array|min:1',
        ]);
        
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja kosong');
            return;
        }

        DB::transaction(function () {
            $order = Order::create([
                // 'customer_name' => Auth::user()->name,
                'customer_name' => $this->customername ?: 'Pelanggan Baru',
                'status' => 'Diproses',
                'pembayaran' => 'Sudah Dibayar',
                'order_type' => $this->orderType ?? 'ditempat',
                'payment_method' => $this->paymentMethod ?? 'Cash',
                'total_price' => $this->getTotal(),
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
            }

            $this->cart = [];
            $this->customername = [];
            $this->orderType = 'ditempat';
            $this->paymentMethod = 'Cash';
        });

        session()->flash('success', 'Pesanan berhasil dibuat!');

    }
}