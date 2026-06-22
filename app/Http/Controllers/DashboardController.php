<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;


class DashboardController extends Controller {
    
    public function index(): View {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        $summary = [
            'today_sales' => Sale::whereDate('sale_date', $today)->sum('total_sales') ?? 0,
            'today_guests' => Sale::whereDate('sale_date', $today)->sum('guests') ?? 0,
            'today_profit' => Sale::whereDate('sale_date', $today)->sum('net_profit') ?? 0,
            'month_sales' => Sale::where('sale_date', '>=', $thisMonth)->sum('total_sales') ?? 0,
            'month_profit' => Sale::where('sale_date', '>=', $thisMonth)->sum('net_profit') ?? 0,
            'month_guests' => Sale::where('sale_date', '>=', $thisMonth)->sum('guests') ?? 0, // MPYA - LIVE
            'total_transactions' => Sale::count() ?? 0,
        ];

        return view('dashboard', compact('summary'));
    }

    public function chartData(): JsonResponse {
        $last30Days = Sale::where('sale_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('sale_date')
            ->get()
            ->groupBy('sale_date');

        $labels = [];
        $salesData = [];
        $profitData = [];
        $guestsData = [];

        foreach ($last30Days as $date => $records) {
            $labels[] = Carbon::parse($date)->format('M d');
            $salesData[] = $records->sum('total_sales');
            $profitData[] = $records->sum('net_profit');
            $guestsData[] = $records->sum('guests');
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Sales (Tsh)',
                    'data' => $salesData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2
                ],
                [
                    'label' => 'Net Profit (Tsh)',
                    'data' => $profitData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2
                ],
                [
                    'label' => 'Guests',
                    'data' => $guestsData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth'  => 2,
                    'type' => 'line',
                    'yAxisID' => 'y1'
                ]
            ]
        ]);
    }

    public function profitAnalysis(): JsonResponse {
        $monthly = Sale::selectRaw('
            YEAR(sale_date) as year,
            MONTH(sale_date) as month,
            SUM(total_sales) as total_sales,
            SUM(market_purchases) as total_purchases,
            SUM(other_expenses) as total_expenses,
            SUM(gross_profit) as total_gross,
            SUM(net_profit) as total_net,
            SUM(guests) as total_guests
        ')
        ->groupBy('year', 'month')
        ->orderByDesc('year')
        ->orderByDesc('month')
        ->limit(12)
        ->get();

        return response()->json($monthly);
    }
}
