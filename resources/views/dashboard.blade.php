@extends('layouts.app')

@section('content')
<!-- Date Filter Row for Daily Stats -->
<div class="row mb-3 animate-fade-in">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body py-2 d-flex align-items-center gap-3">
                <label class="fw-bold text-primary mb-0"><i class="bi bi-funnel me-2"></i>Filter by Date:</label>
                <input type="date" id="dailyFilterDate" class="form-control" style="width: 180px;" 
                       value="{{ $selectedDate ?? now()->toDateString() }}">
                <button class="btn btn-primary btn-sm" onclick="applyDailyFilter()">
                    <i class="bi bi-search me-1"></i>Apply
                </button>
                <span class="text-muted small">Select any date to view daily sales, guests & profit</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 animate-fade-in">
    <!-- Daily Sales Card -->
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Daily Sales</h6>
                        <h3 class="mb-0" id="dailySalesValue">Tsh{{ number_format($summary['today_sales'] ?? 0, 2) }}</h3>
                        <small class="opacity-75" id="dailySalesDate">{{ $selectedDate ? date('M d, Y', strtotime($selectedDate)) : 'Today' }}</small>
                    </div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Daily Guests Card -->
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Daily Guests</h6>
                        <h3 class="mb-0" id="dailyGuestsValue">{{ $summary['today_guests'] ?? 0 }}</h3>
                        <small class="opacity-75" id="dailyGuestsDate">{{ $selectedDate ? date('M d, Y', strtotime($selectedDate)) : 'Today' }}</small>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Daily Net Profit Card -->
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Daily Net Profit</h6>
                        <h3 class="mb-0" id="dailyProfitValue">Tsh{{ number_format($summary['today_net_profit'] ?? $summary['today_profit'] ?? 0, 2) }}</h3>
                        <small class="opacity-75" id="dailyProfitDate">{{ $selectedDate ? date('M d, Y', strtotime($selectedDate)) : 'Today' }}</small>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Transactions Card -->
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
                    <!-- Date Picker for Daily Chart -->
                    <div id="datePickerContainer" class="d-none">
                        <input type="date" id="chartDatePicker" class="form-control form-control-sm" style="width: 150px;">
                    </div>
                    <!-- Month Picker for Monthly Chart -->
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
                    <button class="btn btn-sm btn-primary" onclick="applyMonthlyFilter()">
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
    if (salesChart) salesChart.destroy();

    salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: data.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
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
                        callback: function(value) { return 'Tsh ' + value.toLocaleString('en-US'); }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: {
                        callback: function(value) { return value + ' guests'; }
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
        if (dateFilter) url += (url.includes('?') ? '&' : '?') + 'date=' + dateFilter;
        fetch(url)
            .then(response => response.json())
            .then(data => initChart(data))
            .catch(error => console.error('Error:', error));
    } else {
        // Monthly: show last 12 months by default
        url = '{{ route("api.profit") }}';
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`);
                const salesData = data.map(item => parseFloat(item.total_sales || 0));
                const profitData = data.map(item => parseFloat(item.total_net || 0));
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

// ===== APPLY DAILY FILTER (Update Cards + Chart) =====
function applyDailyFilter() {
    const date = document.getElementById('dailyFilterDate').value;
    if (!date) return;
    loadDailyStats(date);
    currentPeriod = 'daily';
    setActiveButton('daily');
    document.getElementById('chartDatePicker').value = date;
    updateChart('daily', date);
}

function loadDailyStats(date) {
    if (!date) return;
    fetch('{{ route("api.daily-stats") }}?date=' + date)
        .then(response => response.json())
        .then(data => {
            const dateObj = new Date(date);
            const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('dailySalesValue').innerText = 'Tsh ' + parseFloat(data.today_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('dailySalesDate').innerText = formattedDate;
            document.getElementById('dailyGuestsValue').innerText = parseInt(data.today_guests || 0).toLocaleString('en-US');
            document.getElementById('dailyGuestsDate').innerText = formattedDate;
            document.getElementById('dailyProfitValue').innerText = 'Tsh ' + parseFloat(data.today_profit || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('dailyProfitDate').innerText = formattedDate;
        })
        .catch(error => console.error('Error loading daily stats:', error));
}

// ===== APPLY MONTHLY FILTER (Update Summary + Chart with Daily Breakdown) =====
function applyMonthlyFilter() {
    const month = document.getElementById('summaryMonthPicker').value;
    if (!month) return;
    loadSummary(month);
    
    // Switch to monthly view and show daily breakdown for selected month
    currentPeriod = 'monthly';
    setActiveButton('monthly');
    document.getElementById('chartMonthPicker').value = month;
    
    // Fetch daily breakdown for selected month
    fetch('{{ route("api.monthly-daily") }}?month=' + month)
        .then(response => response.json())
        .then(data => {
            initChart(data);
        })
        .catch(error => console.error('Error loading monthly daily breakdown:', error));
}

function loadSummary(month) {
    if (!month) month = document.getElementById('summaryMonthPicker').value;
    if (!month) return;
    fetch('{{ route("api.monthly-summary") }}?month=' + month)
        .then(response => response.json())
        .then(data => {
            document.getElementById('summaryMonthSales').innerText = 'Tsh ' + parseFloat(data.month_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('summaryMonthProfit').innerText = 'Tsh ' + parseFloat(data.month_profit || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('summaryMonthGuests').innerText = parseInt(data.month_guests || 0).toLocaleString('en-US');
            const sales = parseFloat(data.month_sales || 0);
            const profit = parseFloat(data.month_profit || 0);
            const margin = sales > 0 ? ((profit / sales) * 100).toFixed(1) + '%' : '0%';
            document.getElementById('summaryMargin').innerText = margin;
            const maxVal = Math.max(sales, 1);
            document.getElementById('progressSales').style.width = '100%';
            document.getElementById('progressProfit').style.width = Math.min((profit / maxVal) * 100, 100) + '%';
            document.getElementById('progressGuests').style.width = Math.min((parseInt(data.month_guests || 0) / 100) * 100, 100) + '%';
            document.getElementById('progressMargin').style.width = Math.min(parseFloat(margin), 100) + '%';
        })
        .catch(error => console.error('Error loading summary:', error));
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const currentMonth = new Date().toISOString().slice(0, 7);
    
    document.getElementById('dailyFilterDate').value = today;
    document.getElementById('chartDatePicker').value = today;
    document.getElementById('chartMonthPicker').value = currentMonth;
    
    setActiveButton('daily');
    updateChart('daily');
    
    document.getElementById('chartDatePicker').addEventListener('change', function() {
        if (currentPeriod === 'daily') updateChart('daily', this.value);
    });
    
    document.getElementById('chartMonthPicker').addEventListener('change', function() {
        if (currentPeriod === 'monthly') updateChart('monthly');
    });
    
    document.getElementById('dailyFilterDate').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applyDailyFilter();
    });
});
</script>
@endpush