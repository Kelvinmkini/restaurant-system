@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Sales Report</h4>
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download me-1"></i> Download Report
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('sales.download', ['type' => 'filtered', 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
                        <i class="bi bi-calendar-range me-2"></i>Filtered Report ({{ \Carbon\Carbon::parse($fromDate)->format('M d') }} - {{ \Carbon\Carbon::parse($toDate)->format('M d') }})
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('sales.download', ['type' => 'year', 'year' => now()->year]) }}">
                        <i class="bi bi-calendar-year me-2"></i>Full Year {{ now()->year }}
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('sales.download', ['type' => 'all']) }}">
                        <i class="bi bi-archive me-2"></i>All Records
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4" id="filterForm">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" id="fromDate">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" id="toDate">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel me-1"></i>Filter</button>
                <button type="button" class="btn btn-outline-secondary" onclick="resetFilter()">Reset</button>
            </div>
        </form>
        
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Total Sales</small>
                    <h5 class="text-primary mb-0">Tsh{{ number_format($totals['sales'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Purchases</small>
                    <h5 class="text-danger mb-0">Tsh{{ number_format($totals['purchases'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Expenses</small>
                    <h5 class="text-warning mb-0">Tsh{{ number_format($totals['expenses'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Gross Profit</small>
                    <h5 class="text-info mb-0">Tsh{{ number_format($totals['gross'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center bg-light">
                    <small class="text-muted d-block">Net Profit</small>
                    <h5 class="{{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                        Tsh{{ number_format($totals['net'], 2) }}
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
                        <td>Tsh{{ number_format($sale->total_sales, 2) }}</td>
                        <td>Tsh{{ number_format($sale->market_purchases, 2) }}</td>
                        <td>Tsh{{ number_format($sale->other_expenses, 2) }}</td>
                        <td class="text-info">Tsh{{ number_format($sale->gross_profit, 2) }}</td>
                        <td class="{{ $sale->net_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            Tsh{{ number_format($sale->net_profit, 2) }}
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
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
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
                                            {{ $item->foodItem->name ?? 'N/A' }} × {{ $item->quantity }} 
                                            @ Tsh{{ number_format($item->unit_price, 2) }} = 
                                            Tsh{{ number_format($item->total_price, 2) }}
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

@push('scripts')
<script>
// ===== GET DEVICE DATE (Local Time) =====
function getDeviceDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function getFirstDayOfMonth() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    return `${year}-${month}-01`;
}

// ===== INITIALIZE FILTER DATES =====
document.addEventListener('DOMContentLoaded', function() {
    const deviceDate = getDeviceDate();
    const firstDayOfMonth = getFirstDayOfMonth();
    
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');
    
    // Check if URL has existing filter params
    const urlParams = new URLSearchParams(window.location.search);
    const urlFromDate = urlParams.get('from_date');
    const urlToDate = urlParams.get('to_date');
    
    if (urlFromDate && urlToDate) {
        // Use URL params if they exist
        fromDateInput.value = urlFromDate;
        toDateInput.value = urlToDate;
    } else {
        // Use device date: from 1st of current month to today
        fromDateInput.value = firstDayOfMonth;
        toDateInput.value = deviceDate;
        
        // Auto-submit form to load current month data
        document.getElementById('filterForm').submit();
    }
});

// ===== RESET FILTER =====
function resetFilter() {
    const deviceDate = getDeviceDate();
    const firstDayOfMonth = getFirstDayOfMonth();
    
    document.getElementById('fromDate').value = firstDayOfMonth;
    document.getElementById('toDate').value = deviceDate;
    
    document.getElementById('filterForm').submit();
}
</script>
@endpush