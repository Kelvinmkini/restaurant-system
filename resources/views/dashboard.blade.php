@extends('layouts.app')

@section('content')
<!-- Date Filter Row for Daily Stats -->
<div class="row mb-3 animate-fade-in">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body py-2 d-flex align-items-center gap-3">
                <label class="fw-bold text-primary mb-0"><i class="bi bi-funnel me-2"></i>Filter by Date:</label>
                <input type="date" id="dailyFilterDate" class="form-control" style="width: 180px;">
                <button class="btn btn-primary btn-sm" onclick="applyDailyFilter()">
                    <i class="bi bi-search me-1"></i>Apply
                </button>
                <span class="text-muted small" id="dailyFilterDisplay"></span>
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
                        <h3 class="mb-0" id="dailySalesValue">Tsh 0.00</h3>
                        <small class="opacity-75" id="dailySalesDate">Today</small>
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
                        <h3 class="mb-0" id="dailyGuestsValue">0</h3>
                        <small class="opacity-75" id="dailyGuestsDate">Today</small>
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
                        <h3 class="mb-0" id="dailyProfitValue">Tsh 0.00</h3>
                        <small class="opacity-75" id="dailyProfitDate">Today</small>
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
                        <h3 class="mb-0" id="totalTransactionsValue">0</h3>
                        <small class="opacity-75" id="totalTransactionsLabel">All Time</small>
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
                    <div id="datePickerContainer">
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
                    <input type="month" id="summaryMonthPicker" class="form-control form-control-sm" style="width: 140px;">
                    <button class="btn btn-sm btn-primary" onclick="applyMonthlyFilter()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <span class="badge bg-primary fs-6" id="monthFilterDisplay"></span>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Monthly Sales</label>
                    <h4 class="text-primary" id="summaryMonthSales">Tsh 0.00</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" id="progressSales" style="width: 0%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Monthly Net Profit</label>
                    <h4 class="text-success" id="summaryMonthProfit">Tsh 0.00</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" id="progressProfit" style="width: 0%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Total Guests (This Month)</label>
                    <h4 class="text-info" id="summaryMonthGuests">0</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" id="progressGuests" style="width: 0%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Profit Margin</label>
                    <h4 class="text-warning" id="summaryMargin">0%</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" id="progressMargin" style="width: 0%"></div>
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
                            <code class="fs-5 text-warning">(Net Profit / Sales) x 100</code>
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

// ===== GET DEVICE DATE (Local Time) =====
function getDeviceDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function getDeviceMonth() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    return `${year}-${month}`;
}

function formatDateDisplay(dateString) {
    const dateObj = new Date(dateString + 'T00:00:00');
    return dateObj.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

function formatMonthDisplay(monthString) {
    const [year, monthNum] = monthString.split('-');
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${monthNames[parseInt(monthNum) - 1]} ${year}`;
}

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
    
    // Update display dates on cards
    let displayDate;
    if (dateFilter) {
        displayDate = formatDateDisplay(dateFilter);
    } else {
        displayDate = formatDateDisplay(getDeviceDate());
    }
    
    document.getElementById('dailySalesDate').innerText = displayDate;
    document.getElementById('dailyGuestsDate').innerText = displayDate;
    document.getElementById('dailyProfitDate').innerText = displayDate;
    
    let url;
    if (period === 'daily') {
        url = '{{ route("dashboard.chartData") }}';
        if (dateFilter) url += (url.includes('?') ? '&' : '?') + 'date=' + dateFilter;
        fetch(url)
            .then(response => response.json())
            .then(data => initChart(data))
            .catch(error => console.error('Error:', error));
    } else {
        // Monthly: show last 12 months by default
        url = '{{ route("dashboard.profitAnalysis") }}';
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                                   'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const labels = data.map(item => `${monthNames[item.month - 1]} ${item.year}`);
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

// ===== APPLY DAILY FILTER (Update Cards + Chart + Display) =====
function applyDailyFilter() {
    const date = document.getElementById('dailyFilterDate').value;
    if (!date) return;
    loadDailyStats(date);
    loadTotalTransactions(date);
    currentPeriod = 'daily';
    setActiveButton('daily');
    document.getElementById('chartDatePicker').value = date;
    updateChart('daily', date);
    
    // Update display text
    document.getElementById('dailyFilterDisplay').innerText = formatDateDisplay(date);
}

function loadDailyStats(date) {
    if (!date) return;
    fetch('{{ route("dashboard.dailyStats") }}?date=' + date)
        .then(response => response.json())
        .then(data => {
            const formattedDate = formatDateDisplay(date);
            document.getElementById('dailySalesValue').innerText = 'Tsh ' + parseFloat(data.today_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('dailySalesDate').innerText = formattedDate;
            document.getElementById('dailyGuestsValue').innerText = parseInt(data.today_guests || 0).toLocaleString('en-US');
            document.getElementById('dailyGuestsDate').innerText = formattedDate;
            document.getElementById('dailyProfitValue').innerText = 'Tsh ' + parseFloat(data.today_profit || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('dailyProfitDate').innerText = formattedDate;
        })
        .catch(error => console.error('Error loading daily stats:', error));
}

// ===== LOAD TOTAL TRANSACTIONS =====
function loadTotalTransactions(date) {
    if (!date) date = getDeviceDate();
    fetch('{{ route("dashboard.totalTransactions") }}?date=' + date)
        .then(response => response.json())
        .then(data => {
            // Show total transactions (all time)
            document.getElementById('totalTransactionsValue').innerText = data.all || 0;
            document.getElementById('totalTransactionsLabel').innerText = 'All Time';
        })
        .catch(error => console.error('Error loading transactions:', error));
}

// ===== APPLY MONTHLY FILTER (Update Summary + Chart + Display) =====
function applyMonthlyFilter() {
    const month = document.getElementById('summaryMonthPicker').value;
    if (!month) return;
    loadSummary(month);
    
    // Update display text
    document.getElementById('monthFilterDisplay').innerText = formatMonthDisplay(month);
    
    // Switch to monthly view and show daily breakdown for selected month
    currentPeriod = 'monthly';
    setActiveButton('monthly');
    document.getElementById('chartMonthPicker').value = month;
    
    // Fetch daily breakdown for selected month
    fetch('{{ route("dashboard.monthlyDaily") }}?month=' + month)
        .then(response => response.json())
        .then(data => {
            initChart(data);
        })
        .catch(error => console.error('Error loading monthly daily breakdown:', error));
}

function loadSummary(month) {
    if (!month) month = document.getElementById('summaryMonthPicker').value;
    if (!month) return;
    fetch('{{ route("dashboard.monthlySummary") }}?month=' + month)
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

// ===== INITIALIZE ON LOAD - USE DEVICE DATE =====
document.addEventListener('DOMContentLoaded', function() {
    // Get device date (NOT server date)
    const deviceDate = getDeviceDate();
    const deviceMonth = getDeviceMonth();
    
    // Set all date inputs to device date
    document.getElementById('dailyFilterDate').value = deviceDate;
    document.getElementById('chartDatePicker').value = deviceDate;
    document.getElementById('chartMonthPicker').value = deviceMonth;
    document.getElementById('summaryMonthPicker').value = deviceMonth;
    
    // Set display texts
    document.getElementById('dailyFilterDisplay').innerText = formatDateDisplay(deviceDate);
    document.getElementById('monthFilterDisplay').innerText = formatMonthDisplay(deviceMonth);
    
    // Update card dates
    document.getElementById('dailySalesDate').innerText = formatDateDisplay(deviceDate);
    document.getElementById('dailyGuestsDate').innerText = formatDateDisplay(deviceDate);
    document.getElementById('dailyProfitDate').innerText = formatDateDisplay(deviceDate);
    
    // Load initial data
    setActiveButton('daily');
    loadDailyStats(deviceDate);
    loadTotalTransactions(deviceDate);
    loadSummary(deviceMonth);
    updateChart('daily', deviceDate);
    
    // Event listeners
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