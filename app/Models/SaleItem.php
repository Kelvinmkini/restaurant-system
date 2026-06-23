<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model {
    protected $fillable = ['sale_id', 'food_item_id', 'quantity', 'unit_price', 'total_price'];

    protected static function boot() {
        parent::boot();
        
        static::saving(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }

    public function sale(): BelongsTo {
        return $this->belongsTo(Sale::class);
    }

    public function foodItem(): BelongsTo {
        return $this->belongsTo(FoodItem::class)->withDefault([
            'name' => '[Deleted Item]',
            'price' => 0,
        ]);
    }
}