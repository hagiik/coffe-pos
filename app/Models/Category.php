<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['name', 'slug', 'img'];

    public function products() {
        return $this->hasMany(Product::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'img'])
            ->setDescriptionForEvent(fn(string $eventName) => "Category has been {$eventName}");
    }
}
