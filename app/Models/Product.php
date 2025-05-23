<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'category_id','images'];

    protected $casts = [
        'images' => 'array',
    ];
    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function variants()
    {
        return $this->hasMany(product_variants::class);
    }
    public function orders() {
        return $this->hasMany(Order::class);
    }
}
