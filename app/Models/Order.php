<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use HasFactory, LogsActivity;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_name', 'status', 'pembayaran', 'order_type', 'payment_method', 'total_price'])
            ->setDescriptionForEvent(fn(string $eventName) => "Order has been {$eventName}");
    }
}
