<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\FoodItem;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SaleController extends Controller {
    
    public function create(): View {
        $foodItems = FoodItem::where('is_active', true)->get();
        return view('sales.create', compact('foodItems'));
    }

    public function store(Request $request): RedirectResponse {
        $validated = $request->validate([
            'guests' => 'required|integer|min:0',
            'sale_date' => 'required|date',
            'market_purchases' => 'required|numeric|min:0',
            'other_expenses' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0', // NEW: custom sale price
        ]);

        // Calculate total sales from items using CUSTOM sale prices
        $totalSales = 0;
        foreach ($validated['items'] as $item) {
            $totalSales += $item['unit_price'] * $item['quantity'];
        }

        // Create sale (profit calculated automatically by model boot)
        $sale = Sale::create([
            'guests' => $validated['guests'],
            'total_sales' => $totalSales,
            'market_purchases' => $validated['market_purchases'],
            'other_expenses' => $validated['other_expenses'],
            'sale_date' => $validated['sale_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create sale items with CUSTOM prices
        foreach ($validated['items'] as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'food_item_id' => $item['food_item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'], // User's custom price
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Sale recorded successfully! Gross Profit: $' . number_format($sale->gross_profit, 2));
    }

    public function report(Request $request): View {
        $query = Sale::with('items.foodItem');
        
        if ($request->filled('from_date')) {
            $query->where('sale_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('sale_date', '<=', $request->to_date);
        }
        
        $sales = $query->orderByDesc('sale_date')->paginate(20);
        
        $totals = [
            'sales' => $sales->sum('total_sales'),
            'purchases' => $sales->sum('market_purchases'),
            'expenses' => $sales->sum('other_expenses'),
            'gross' => $sales->sum('gross_profit'),
            'net' => $sales->sum('net_profit'),
        ];

        return view('sales.report', compact('sales', 'totals'));
    }
}
