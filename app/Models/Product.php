<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['name', 'slug','description', 'category_id','images'];

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'category_id', 'images'])
            ->setDescriptionForEvent(fn(string $eventName) => "Product has been {$eventName}");
    }
}
