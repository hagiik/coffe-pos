<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'status',
        'pembayaran',
        'order_type',
        'payment_method',
        'total_price',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function items()
{
    return $this->hasMany(OrderItem::class);
}
}
