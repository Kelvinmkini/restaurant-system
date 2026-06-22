@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4 animate-fade-in">
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Today's Sales</h6>
                        <h3 class="mb-0">Tsh{{ number_format($summary['today_sales'] ?? 0, 2) }}</h3>
                    </div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Today's Guests</h6>
                        <h3 class="mb-0">{{ $summary['today_guests'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Today's Net Profit</h6>
                        <h3 class="mb-0">Tsh{{ number_format($summary['today_net_profit'] ?? $summary['today_profit'] ?? 0, 2) }}</h3>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Total Transactions</h6>
                        <h3 class="mb-0">{{ $summary['total_transactions'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-receipt fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Sales & Profit Trends</h5>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Date Picker -->
                    <div id="datePickerContainer" class="d-none">
                        <input type="date" id="chartDatePicker" class="form-control form-control-sm" style="width: 150px;">
                    </div>
                    <!-- Month Picker -->
                    <div id="monthPickerContainer" class="d-none">
                        <input type="month" id="chartMonthPicker" class="form-control form-control-sm" style="width: 150px;">
                    </div>
                    <div class="btn-group btn-group-sm" id="chartPeriodButtons">
                        <button class="btn btn-primary active-chart-btn" id="btnDaily" onclick="updateChart('daily')">
                            <i class="bi bi-calendar-day me-1"></i>Daily
                        </button>
                        <button class="btn btn-outline-primary" id="btnMonthly" onclick="updateChart('monthly')">
                            <i class="bi bi-calendar-month me-1"></i>Monthly
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-success"></i>Monthly Summary</h5>
                <!-- Month Picker for Summary -->
                <div class="d-flex align-items-center gap-2">
                    <input type="month" id="summaryMonthPicker" class="form-control form-control-sm" 
                           style="width: 140px;" value="{{ $selectedMonth ?? now()->format('Y-m') }}">
                    <button class="btn btn-sm btn-primary" onclick="loadSummary()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="text-muted small">Monthly Sales</label>
                    <h4 class="text-primary" id="summaryMonthSales">Tsh{{ number_format($summary['month_sales'] ?? 0, 2) }}</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" id="progressSales" style="width: 75%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Monthly Net Profit</label>
                    <h4 class="text-success" id="summaryMonthProfit">Tsh{{ number_format($summary['month_profit'] ?? 0, 2) }}</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" id="progressProfit" style="width: 60%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Total Guests (This Month)</label>
                    <h4 class="text-info" id="summaryMonthGuests">{{ $summary['month_guests'] ?? 0 }}</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" id="progressGuests" style="width: 55%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Profit Margin</label>
                    <h4 class="text-warning" id="summaryMargin">
                        @if(($summary['month_sales'] ?? 0) > 0)
                            {{ number_format((($summary['month_profit'] ?? 0) / ($summary['month_sales'] ?? 0)) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" id="progressMargin" style="width: 45%"></div>
                    </div>
                </div>
                <hr>
                <a href="{{ route('sales.create') }}" class="btn btn-primary w-100 btn-custom mb-2">
                    <i class="bi bi-plus-lg me-2"></i>Record New Sale
                </a>
                <a href="{{ route('sales.report') }}" class="btn btn-outline-primary w-100 btn-custom">
                    <i class="bi bi-file-earmark-text me-2"></i>View Full Report
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-calculator me-2 text-info"></i>Profit Calculation Formula</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-muted">Gross Profit</h6>
                            <code class="fs-5 text-primary">Sales - Purchases</code>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-muted">Net Profit</h6>
                            <code class="fs-5 text-success">Gross Profit - Expenses</code>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-muted">Profit Margin</h6>
                            <code class="fs-5 text-warning">(Net Profit / Sales) × 100</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Active button styling */
    .active-chart-btn {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
        font-weight: 600;
    }
    
    .inactive-chart-btn {
        background-color: transparent !important;
        color: #0d6efd !important;
        border-color: #0d6efd !important;
    }
    
    .inactive-chart-btn:hover {
        background-color: #e7f1ff !important;
    }
</style>
@endpush

@push('scripts')
<script>
let salesChart = null;
let currentPeriod = 'daily';

function setActiveButton(period) {
    const btnDaily = document.getElementById('btnDaily');
    const btnMonthly = document.getElementById('btnMonthly');
    const datePicker = document.getElementById('datePickerContainer');
    const monthPicker = document.getElementById('monthPickerContainer');
    
    if (period === 'daily') {
        btnDaily.className = 'btn btn-primary active-chart-btn';
        btnMonthly.className = 'btn btn-outline-primary inactive-chart-btn';
        datePicker.classList.remove('d-none');
        monthPicker.classList.add('d-none');
    } else {
        btnDaily.className = 'btn btn-outline-primary inactive-chart-btn';
        btnMonthly.className = 'btn btn-primary active-chart-btn';
        datePicker.classList.add('d-none');
        monthPicker.classList.remove('d-none');
    }
}

function initChart(data) {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    if (salesChart) {
        salesChart.destroy();
    }

    salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: data.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label.includes('Guests')) {
                                label += context.parsed.y;
                            } else {
                                label += 'Tsh ' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Tsh ' + value.toLocaleString('en-US');
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' guests';
                        }
                    }
                }
            }
        }
    });
}

function updateChart(period, dateFilter = null) {
    currentPeriod = period;
    setActiveButton(period);
    
    let url;
    if (period === 'daily') {
        url = '{{ route("api.chart") }}';
        if (dateFilter) {
            url += (url.includes('?') ? '&' : '?') + 'date=' + dateFilter;
        }
        fetch(url)
            .then(response => response.json())
            .then(data => initChart(data))
            .catch(error => console.error('Error:', error));
    } else {
        url = '{{ route("api.profit") }}';
        if (dateFilter) {
            url += (url.includes('?') ? '&' : '?') + 'month=' + dateFilter;
        }
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`);
                const salesData = data.map(item => parseFloat(item.total_sales));
                const profitData = data.map(item => parseFloat(item.total_net));
                const guestsData = data.map(item => parseFloat(item.total_guests || 0));
                
                initChart({
                    labels: labels.reverse(),
                    datasets: [
                        {
                            label: 'Total Sales (Tsh)',
                            data: salesData.reverse(),
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2
                        },
                        {
                            label: 'Net Profit (Tsh)',
                            data: profitData.reverse(),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2
                        },
                        {
                            label: 'Guests',
                            data: guestsData.reverse(),
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            type: 'line',
                            yAxisID: 'y1'
                        }
                    ]
                });
            })
            .catch(error => console.error('Error:', error));
    }
}

// ===== LOAD SUMMARY FOR SELECTED MONTH =====
function loadSummary() {
    const month = document.getElementById('summaryMonthPicker').value;
    if (!month) return;
    
    fetch('{{ route("api.monthly-summary") }}?month=' + month)
        .then(response => response.json())
        .then(data => {
            // Update summary values
            document.getElementById('summaryMonthSales').innerText = 
                'Tsh ' + parseFloat(data.month_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('summaryMonthProfit').innerText = 
                'Tsh ' + parseFloat(data.month_profit || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('summaryMonthGuests').innerText = 
                parseInt(data.month_guests || 0).toLocaleString('en-US');
            
            // Calculate margin
            const sales = parseFloat(data.month_sales || 0);
            const profit = parseFloat(data.month_profit || 0);
            const margin = sales > 0 ? ((profit / sales) * 100).toFixed(1) + '%' : '0%';
            document.getElementById('summaryMargin').innerText = margin;
            
            // Update progress bars (max 100% relative to sales)
            const maxVal = Math.max(sales, 1);
            document.getElementById('progressSales').style.width = '100%';
            document.getElementById('progressProfit').style.width = Math.min((profit / maxVal) * 100, 100) + '%';
            document.getElementById('progressGuests').style.width = Math.min((parseInt(data.month_guests || 0) / 100) * 100, 100) + '%';
            document.getElementById('progressMargin').style.width = Math.min(parseFloat(margin), 100) + '%';
        })
        .catch(error => {
            console.error('Error loading summary:', error);
            alert('Failed to load summary for selected month');
        });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('chartDatePicker').value = today;
    
    // Set default month to current month
    const currentMonth = new Date().toISOString().slice(0, 7);
    document.getElementById('chartMonthPicker').value = currentMonth;
    
    setActiveButton('daily');
    updateChart('daily');
    
    // Event listeners for chart pickers
    document.getElementById('chartDatePicker').addEventListener('change', function() {
        if (currentPeriod === 'daily') {
            updateChart('daily', this.value);
        }
    });
    
    document.getElementById('chartMonthPicker').addEventListener('change', function() {
        if (currentPeriod === 'monthly') {
            updateChart('monthly', this.value);
        }
    });
    
    // Summary month picker - auto-load on change
    document.getElementById('summaryMonthPicker').addEventListener('change', function() {
        loadSummary();
    });
});
</script>
@endpush