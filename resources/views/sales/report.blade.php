@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Sales Report</h4>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('sales.report') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Total Sales</small>
                    <h5 class="text-primary mb-0">${{ number_format($totals['sales'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Purchases</small>
                    <h5 class="text-danger mb-0">${{ number_format($totals['purchases'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Expenses</small>
                    <h5 class="text-warning mb-0">${{ number_format($totals['expenses'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Gross Profit</small>
                    <h5 class="text-info mb-0">${{ number_format($totals['gross'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Net Profit</small>
                    <h5 class="{{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                        ${{ number_format($totals['net'], 2) }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Guests</th>
                        <th>Sales</th>
                        <th>Purchases</th>
                        <th>Expenses</th>
                        <th>Gross Profit</th>
                        <th>Net Profit</th>
                        <th>Margin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                        <td>{{ $sale->guests }}</td>
                        <td>${{ number_format($sale->total_sales, 2) }}</td>
                        <td>${{ number_format($sale->market_purchases, 2) }}</td>
                        <td>${{ number_format($sale->other_expenses, 2) }}</td>
                        <td class="text-info">${{ number_format($sale->gross_profit, 2) }}</td>
                        <td class="{{ $sale->net_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            ${{ number_format($sale->net_profit, 2) }}
                        </td>
                        <td>
                            @if($sale->total_sales > 0)
                                <span class="badge bg-{{ $sale->net_profit >= 0 ? 'success' : 'danger' }}">
                                    {{ number_format(($sale->net_profit / $sale->total_sales) * 100, 1) }}%
                                </span>
                            @else
                                <span class="badge bg-secondary">0%</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" 
                                    data-bs-target="#details{{ $sale->id }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="details{{ $sale->id }}">
                        <td colspan="9" class="bg-light">
                            <div class="p-3">
                                <h6>Items Sold:</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($sale->items as $item)
                                        <li>
                                            {{ $item->foodItem->name }} × {{ $item->quantity }} 
                                            @ ${{ number_format($item->unit_price, 2) }} = 
                                            ${{ number_format($item->total_price, 2) }}
                                        </li>
                                    @endforeach
                                </ul>
                                @if($sale->notes)
                                <small class="text-muted mt-2 d-block">Notes: {{ $sale->notes }}</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No sales records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection