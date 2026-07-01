<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function create()
    {
        $foodItems = FoodItem::where('is_active', true)->with('category')->orderBy('name')->get();
        return view('sales.create', compact('foodItems'));
    }

    public function store(Request $request)
    {
        // Debug: Log all incoming data
        Log::info('Sale Store Request Data:', $request->all());

        $validated = $request->validate([
            'sale_date' => 'required|date',
            'guests' => 'required|integer|min:0',
            'market_purchases' => 'required|numeric|min:0',
            'other_expenses' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Debug: Log validated data
        Log::info('Sale Store Validated Data:', $validated);

        DB::beginTransaction();

        try {
            // Hesabu total_sales kutoka kwenye items ZOTE
            $totalSales = 0;
            $itemDetails = [];
            
            foreach ($validated['items'] as $index => $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $totalSales += $lineTotal;
                $itemDetails[] = [
                    'index' => $index,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal
                ];
            }

            // Debug: Log calculation
            Log::info('Sale Store Calculation:', [
                'total_sales' => $totalSales,
                'items_count' => count($validated['items']),
                'items' => $itemDetails
            ]);

            // Create sale - gross_profit na net_profit zitahesabiwa na boot()
            $sale = Sale::create([
                'sale_date' => $validated['sale_date'],
                'guests' => $validated['guests'],
                'total_sales' => $totalSales,
                'market_purchases' => $validated['market_purchases'],
                'other_expenses' => $validated['other_expenses'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Debug: Log created sale
            Log::info('Sale Created:', ['sale_id' => $sale->id, 'total_sales' => $sale->total_sales]);

            // Save items
            foreach ($validated['items'] as $index => $item) {
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
                
                Log::info('SaleItem Created:', [
                    'index' => $index,
                    'sale_item_id' => $saleItem->id,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);
            }

            DB::commit();

            Log::info('Sale Transaction Committed Successfully');

            // Flash message na redirect kwenye report
            return redirect()->route('sales.report')
                ->with('success', 'Sale recorded successfully! Total: Tsh' . number_format($totalSales, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Sale Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

   public function report(Request $request)
{
    // Get device date from request or use current date
    $deviceDate = now()->toDateString();
    
    $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->get('to_date', $deviceDate);

    $sales = Sale::with('items.foodItem')
        ->whereBetween('sale_date', [$fromDate, $toDate])
        ->orderBy('sale_date', 'desc')
        ->paginate(20);

    $totals = [
        'sales' => $sales->sum('total_sales'),
        'purchases' => $sales->sum('market_purchases'),
        'expenses' => $sales->sum('other_expenses'),
        'gross' => $sales->sum('gross_profit'),
        'net' => $sales->sum('net_profit'),
    ];

    return view('sales.report', compact('sales', 'totals', 'fromDate', 'toDate'));
}

    public function edit(Sale $sale)
    {
        $foodItems = FoodItem::where('is_active', true)->with('category')->orderBy('name')->get();
        return view('sales.edit', compact('sale', 'foodItems'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'guests' => 'required|integer|min:0',
            'market_purchases' => 'required|numeric|min:0',
            'other_expenses' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Hesabu total_sales kutoka kwenye items ZOTE
            $totalSales = 0;
            foreach ($validated['items'] as $item) {
                $totalSales += $item['quantity'] * $item['unit_price'];
            }

            // Update sale - gross_profit na net_profit zitahesabiwa na boot()
            $sale->update([
                'sale_date' => $validated['sale_date'],
                'guests' => $validated['guests'],
                'total_sales' => $totalSales,
                'market_purchases' => $validated['market_purchases'],
                'other_expenses' => $validated['other_expenses'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete old items and save new ones
            $sale->items()->delete();
            foreach ($validated['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('sales.report')
                ->with('success', 'Sale updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Sale $sale)
    {
        $sale->items()->delete();
        $sale->delete();

        return redirect()->route('sales.report')
            ->with('success', 'Sale record deleted successfully.');
    }

    public function download(Request $request)
    {
        $type = $request->get('type', 'filtered');
        
        $query = Sale::with('items.foodItem');

        switch ($type) {
            case 'year':
                $year = $request->get('year', now()->year);
                $query->whereYear('sale_date', $year);
                $filename = "sales_report_{$year}.csv";
                break;
                
            case 'all':
                $filename = "sales_report_all.csv";
                break;
                
            case 'filtered':
            default:
                $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
                $toDate = $request->get('to_date', now()->endOfMonth()->toDateString());
                $query->whereBetween('sale_date', [$fromDate, $toDate]);
                $filename = "sales_report_{$fromDate}_to_{$toDate}.csv";
                break;
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date', 'Guests', 'Total Sales', 'Market Purchases', 'Other Expenses', 
                'Gross Profit', 'Net Profit', 'Margin %', 'Items Sold', 'Notes'
            ]);

            foreach ($sales as $sale) {
                $items = $sale->items->map(function($item) {
                    return ($item->foodItem->name ?? 'N/A') . ' x' . $item->quantity;
                })->implode('; ');

                $margin = $sale->total_sales > 0 
                    ? number_format(($sale->net_profit / $sale->total_sales) * 100, 2) 
                    : '0.00';

                fputcsv($file, [
                    $sale->sale_date->format('Y-m-d'),
                    $sale->guests,
                    $sale->total_sales,
                    $sale->market_purchases,
                    $sale->other_expenses,
                    $sale->gross_profit,
                    $sale->net_profit,
                    $margin,
                    $items,
                    $sale->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}