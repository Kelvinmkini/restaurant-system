<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model {
    protected $fillable = [
        'guests', 'total_sales', 'market_purchases', 
        'other_expenses', 'gross_profit', 'net_profit', 
        'sale_date', 'notes'
    ];
    protected $casts = [
        'sale_date' => 'date',
        'total_sales' => 'decimal:2',
        'market_purchases' => 'decimal:2',
        'other_expenses' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    protected static function boot() {
        parent::boot();
        static::saving(function ($sale) {
            // Automatic Calculation Engine
            $sale->gross_profit = $sale->total_sales - $sale->market_purchases;
            $sale->net_profit = $sale->gross_profit - $sale->other_expenses;
        });
    }

    public function items(): HasMany {
        return $this->hasMany(SaleItem::class);
    }
}