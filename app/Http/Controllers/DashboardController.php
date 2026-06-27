<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard');
    }

    // ===== TOTAL TRANSACTIONS =====
    public function totalTransactions(Request $request): JsonResponse
    {
        $date = $request->get('date', now()->toDateString());
        $selectedDate = Carbon::parse($date);
        
        // Idadi ya transactions za leo
        $todayTransactions = Sale::whereDate('sale_date', $selectedDate)->count();
        
        // Idadi ya transactions za mwezi
        $monthTransactions = Sale::whereYear('sale_date', $selectedDate->year)
            ->whereMonth('sale_date', $selectedDate->month)
            ->count();
            
        // Idadi ya transactions za mwaka
        $yearTransactions = Sale::whereYear('sale_date', $selectedDate->year)->count();
        
        // Idadi ya transactions zote
        $allTransactions = Sale::count();

        return response()->json([
            'today' => $todayTransactions,
            'month' => $monthTransactions,
            'year' => $yearTransactions,
            'all' => $allTransactions,
        ]);
    }

    // ===== CHART DATA - DAILY =====
    public function chartData(Request $request): JsonResponse {
        $date = $request->get('date');
        
        if ($date) {
            $selectedDate = Carbon::parse($date);
            $startDate = $selectedDate->copy()->subDays(6);
            $endDate = $selectedDate->copy();
            
            $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
                ->orderBy('sale_date')
                ->get()
                ->groupBy('sale_date');
        } else {
            $sales = Sale::where('sale_date', '>=', Carbon::now()->subDays(30))
                ->orderBy('sale_date')
                ->get()
                ->groupBy('sale_date');
        }

        $labels = [];
        $salesData = [];
        $profitData = [];
        $guestsData = [];

        foreach ($sales as $date => $records) {
            $carbonDate = Carbon::parse($date);
            $labels[] = $carbonDate->format('M d, Y');
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
                    'borderWidth' => 2,
                    'type' => 'line',
                    'yAxisID' => 'y1'
                ]
            ]
        ]);
    }

    // ===== PROFIT ANALYSIS - MONTHLY =====
    public function profitAnalysis(): JsonResponse {
        $monthly = Sale::selectRaw('
            YEAR(sale_date) as year,
            MONTH(sale_date) as month,
            SUM(total_sales) as total_sales,
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

    // ===== MONTHLY DAILY BREAKDOWN =====
    public function monthlyDailyBreakdown(Request $request): JsonResponse {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);
        
        $dailyData = Sale::selectRaw('
            DAY(sale_date) as day,
            SUM(total_sales) as total_sales,
            SUM(net_profit) as total_net,
            SUM(guests) as total_guests
        ')
        ->whereYear('sale_date', $year)
        ->whereMonth('sale_date', $monthNum)
        ->groupBy('day')
        ->orderBy('day')
        ->get();
        
        $labels = [];
        $salesData = [];
        $profitData = [];
        $guestsData = [];
        
        foreach ($dailyData as $record) {
            $labels[] = 'Day ' . $record->day;
            $salesData[] = $record->total_sales;
            $profitData[] = $record->total_net;
            $guestsData[] = $record->total_guests;
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
                    'borderWidth' => 2,
                    'type' => 'line',
                    'yAxisID' => 'y1'
                ]
            ]
        ]);
    }

    // ===== MONTHLY SUMMARY =====
    public function monthlySummary(Request $request): JsonResponse
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);
        
        $startOfMonth = Carbon::create($year, $monthNum, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $monthNum, 1)->endOfMonth();
        
        $summary = [
            'month_sales' => Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
                ->sum('total_sales') ?? 0,
            'month_profit' => Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
                ->sum('net_profit') ?? 0,
            'month_guests' => Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
                ->sum('guests') ?? 0,
        ];
        
        return response()->json($summary);
    }

    // ===== DAILY STATS =====
    public function dailyStats(Request $request): JsonResponse
    {
        $date = $request->get('date', now()->toDateString());
        $selectedDate = Carbon::parse($date);
        
        $stats = [
            'today_sales' => Sale::whereDate('sale_date', $selectedDate)->sum('total_sales') ?? 0,
            'today_guests' => Sale::whereDate('sale_date', $selectedDate)->sum('guests') ?? 0,
            'today_profit' => Sale::whereDate('sale_date', $selectedDate)->sum('net_profit') ?? 0,
        ];
        
        return response()->json($stats);
    }
}