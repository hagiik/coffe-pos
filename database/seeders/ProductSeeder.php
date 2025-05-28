<?php

namespace Database\Seeders;

use App\Models\product_variants;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $sizes = ['Small', 'Medium', 'Large'];
        $temperatures = ['Hot', 'Cold'];

        $categories = \App\Models\Category::pluck('id')->toArray();

        // Buat 10 produk acak
        for ($i = 1; $i <= 10; $i++) {
            $name = fake()->words(2, true);
            $product = Product::create([
                'name' => ucfirst($name),
                'slug' => Str::slug($name) . '-' . Str::random(5),
                'description' => fake()->sentence(),
                'images' => json_encode([]), // bisa diisi ['produk1.jpg', ...]
                'category_id' => fake()->randomElement($categories),
            ]);

            // Buat kombinasi variant untuk tiap produk
            foreach ($sizes as $size) {
                foreach ($temperatures as $temp) {
                    product_variants::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'temperature' => $temp,
                        'price' => fake()->numberBetween(10000, 30000),
                    ]);
                }
            }
        }
    }
}
