<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class product_variants extends Model
{
    use HasFactory, LogsActivity;
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
        public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'size', 'temperature', 'price'])
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}
