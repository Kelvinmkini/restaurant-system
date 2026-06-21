<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodItem extends Model {
    protected $fillable = ['name', 'category', 'price', 'cost_price', 'is_active'];
    
    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    public function saleItems(): HasMany {
        return $this->hasMany(SaleItem::class);
    }
}