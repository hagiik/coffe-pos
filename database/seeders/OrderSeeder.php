<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product_variants;
use App\Models\ProductVariant;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa varian produk
        $variants = product_variants::inRandomOrder()->take(3)->get();

        foreach (range(1, 5) as $i) {
            $order = Order::create([
                'customer_name' => 'Pelanggan ' . $i,
                'status' => 'Menunggu',
                'pembayaran' => 'Menunggu',
                'order_type' => 'ditempat',
                'payment_method' => 'Cash',
                'total_price' => 0, // Akan di-update setelah item ditambahkan
            ]);

            $total = 0;

            foreach ($variants as $variant) {
                $quantity = rand(1, 3);
                $subtotal = $variant->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $variant->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total_price' => $total]);
        }
    }
}
