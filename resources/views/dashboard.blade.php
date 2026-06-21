@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4 animate-fade-in">
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Today's Sales</h6>
                        <h3 class="mb-0">${{ number_format($summary['today_sales'], 2) }}</h3>
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
                        <h3 class="mb-0">{{ $summary['today_guests'] }}</h3>
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
                        <h6 class="text-uppercase mb-2 opacity-75">Today's Profit</h6>
                        <h3 class="mb-0">${{ number_format($summary['today_profit'], 2) }}</h3>
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
                        <h3 class="mb-0">{{ $summary['total_transactions'] }}</h3>
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
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Sales & Profit Trends (Last 30 Days)</h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active" onclick="updateChart('daily')">Daily</button>
                    <button class="btn btn-outline-primary"onclick="loadProfitAnalysis()">Monthly</button>
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
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-success"></i>Monthly Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="text-muted small">Monthly Sales</label>
                    <h4 class="text-primary">${{ number_format($summary['month_sales'], 2) }}</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: 75%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Monthly Net Profit</label>
                    <h4 class="text-success">${{ number_format($summary['month_profit'], 2) }}</h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 60%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-muted small">Profit Margin</label>
                    <h4 class="text-warning">
                        @if($summary['month_sales'] > 0)
                            {{ number_format(($summary['month_profit'] / $summary['month_sales']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </h4>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 45%"></div>
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
@push('scripts')
<script>
let salesChart = null;

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
                                label += '$' + context.parsed.y.toFixed(2);
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
                            return '$' + value;
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

function updateChart(period) {
    fetch('{{ route("api.chart") }}')
        .then(response => response.json())
        .then(data => initChart(data))
        .catch(error => console.error('Error:', error));
}

function loadProfitAnalysis() {
    fetch('{{ route("api.profit") }}')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`);
            const salesData = data.map(item => parseFloat(item.total_sales));
            const profitData = data.map(item => parseFloat(item.total_net));
            
            initChart({
                labels: labels.reverse(),
                datasets: [
                    {
                        label: 'Total Sales ($)',
                        data: salesData.reverse(),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Net Profit ($)',
                        data: profitData.reverse(),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    }
                ]
            });
            });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    updateChart('daily');
});
</script>
@endpush