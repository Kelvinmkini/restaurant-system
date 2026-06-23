<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'color', 'sort_order', 'is_active'];

    // ADD THIS
    public function foodItems()
    {
        return $this->hasMany(FoodItem::class);
    }
}