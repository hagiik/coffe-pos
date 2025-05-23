<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_variants extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'size',
        'temperature',
        'price',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
        public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }
}
