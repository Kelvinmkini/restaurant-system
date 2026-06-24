@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="card shadow-lg">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0"><i class="bi bi-plus-circle-dotted me-2 text-primary"></i>Record New Sale</h4>
                <p class="text-muted mb-0 small">Select items, adjust sale prices if needed. Profit auto-calculated.</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Sale Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="text" name="sale_date" class="form-control" required 
                                       id="saleDate" placeholder="Select date" readonly>
                            </div>
                            <small class="text-muted">Click to open calendar</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Number of Guests</label>
                            <input type="number" name="guests" class="form-control" min="0" required 
                                   placeholder="0" id="guestsInput" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Market Purchases (Tsh)</label>
                            <input type="number" name="market_purchases" class="form-control" step="0.01" min="0" 
                                   required placeholder="0.00" id="marketPurchases" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Other Expenses (Tsh)</label>
                            <input type="number" name="other_expenses" class="form-control" step="0.01" min="0" 
                                   required placeholder="0.00" id="otherExpenses" value="0">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">Food & Drink Items Sold</label>
                            <span class="badge bg-info text-dark">You can change the sale price below</span>
                        </div>
                        
                        <div id="itemsContainer">
                            <div class="row item-row mb-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Item</label>
                                    <select name="items[0][food_item_id]" class="form-select item-select" required>
                                        <option value="">Select Item</option>
                                        @foreach($foodItems->groupBy('category.name') as $categoryName => $items)
                                            <optgroup label="{{ $categoryName ?? 'Uncategorized' }}">
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-price="{{ $item->price }}">
                                                        {{ $item->name }} (Menu: Tsh{{ number_format($item->price, 2) }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Qty</label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input" 
                                           placeholder="0" min="0" value="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Sale Price (Tsh) <span class="text-primary">*</span></label>
                                    <input type="number" name="items[0][unit_price]" class="form-control unit-price-input" 
                                           step="0.01" min="0" required placeholder="0.00">
                                    <small class="text-muted">Default: menu price</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-muted">Line Total</label>
                                    <input type="text" class="form-control item-total bg-light" readonly placeholder="Tsh0.00">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-item" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addItem">
                            <i class="bi bi-plus-lg me-1"></i>Add Another Item
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>

                    <div class="card bg-light mb-4 border-primary border-2">
                        <div class="card-body">
                            <h6 class="text-primary mb-3"><i class="bi bi-calculator me-2"></i>Auto-Calculated Summary</h6>
                            <div class="row">
                                <div class="col-md-3 text-center border-end">
                                    <small class="text-muted d-block">Total Sales</small>
                                    <h4 class="text-primary mb-0" id="displayTotalSales">Tsh0.00</h4>
                                </div>
                                <div class="col-md-3 text-center border-end">
                                    <small class="text-muted d-block">Gross Profit</small>
                                    <h4 class="text-info mb-0" id="displayGrossProfit">Tsh0.00</h4>
                                    <small class="text-muted">Sales - Purchases</small>
                                </div>
                                <div class="col-md-3 text-center border-end">
                                    <small class="text-muted d-block">Net Profit</small>
                                    <h4 class="mb-0" id="displayNetProfit">Tsh0.00</h4>
                                    <small class="text-muted">Gross - Expenses</small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <small class="text-muted d-block">Profit Margin</small>
                                    <h4 class="text-warning mb-0" id="displayMargin">0%</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-custom btn-lg">
                            <i class="bi bi-check-lg me-2"></i>Save Sale Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Flatpickr styling improvements */
    .flatpickr-calendar {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        z-index: 9999 !important;
    }
    .flatpickr-day.selected {
        background: #3498db;
        border-color: #3498db;
    }
    .flatpickr-day:hover {
        background: #e3f2fd;
    }
    .flatpickr-current-month {
        font-size: 1.1em;
    }
    .flatpickr-monthDropdown-months {
        font-size: 0.95em;
    }
    
    /* Ensure datepicker is visible */
    #saleDate {
        cursor: pointer;
        background-color: #fff;
    }
    
    /* Make sure the input is clickable */
    .input-group-text {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
let itemCount = 1;

// Initialize Date Picker
function initDatePicker() {
    // Pata tarehe ya leo kutoka kwenye kifaa (device)
    const deviceDate = new Date();
    const year = deviceDate.getFullYear();
    const month = String(deviceDate.getMonth() + 1).padStart(2, '0');
    const day = String(deviceDate.getDate()).padStart(2, '0');
    const todayString = `${year}-${month}-${day}`;

    // Weka tarehe ya leo kwenye input
    const dateInput = document.getElementById('saleDate');
    dateInput.value = todayString;

    // Initialize flatpickr - HAKUNA LIMITI YA TAREHE
    const fp = flatpickr("#saleDate", {
        dateFormat: "Y-m-d",
        defaultDate: todayString,
        allowInput: false,        // Zuia kuandika moja kwa moja
        clickOpens: true,         // Fungua kwa kubofya
        disableMobile: false,     // Ruhusu kwenye simu
        
        // Ruhusu kuchagua mwaka, mwezi, na tarehe
        monthSelectorType: "dropdown",  // Dropdown ya miezi
        yearSelectorType: "dropdown",  // Dropdown ya miaka
        
        // HAKUNA minDate wala maxDate - tarehe yoyote inaruhusiwa
        
        // Settings za lugha
        locale: {
            firstDayOfWeek: 1,  // Jumatatu kama siku ya kwanza
            weekdays: {
                shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            }
        },
        
        // Callback function
        onReady: function(selectedDates, dateStr, instance) {
            console.log('Flatpickr initialized successfully');
            console.log('Current date:', dateStr);
        },
        
        onChange: function(selectedDates, dateStr, instance) {
            console.log('Date selected:', dateStr);
        }
    });

    // Bofya kwenye input group pia ifungue kalenda
    document.querySelector('.input-group-text').addEventListener('click', function() {
        fp.open();
    });
}

// Calculate each row
function calculateRow(row) {
    const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
    const totalField = row.querySelector('.item-total');

    const total = qty * price;
    totalField.value = 'Tsh' + total.toFixed(2);

    return total;
}

// Calculate all totals
function calculateTotals() {
    let totalSales = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        totalSales += calculateRow(row);
    });

    const marketPurchases = parseFloat(document.getElementById('marketPurchases').value) || 0;
    const otherExpenses = parseFloat(document.getElementById('otherExpenses').value) || 0;

    const grossProfit = totalSales - marketPurchases;
    const netProfit = grossProfit - otherExpenses;
    const margin = totalSales > 0 ? (netProfit / totalSales) * 100 : 0;

    document.getElementById('displayTotalSales').innerText = 'Tsh' + totalSales.toFixed(2);
    document.getElementById('displayGrossProfit').innerText = 'Tsh' + grossProfit.toFixed(2);
    
    const netElement = document.getElementById('displayNetProfit');
    netElement.innerText = 'Tsh' + netProfit.toFixed(2);
    netElement.className = 'mb-0 ' + (netProfit >= 0 ? 'text-success' : 'text-danger');
    
    document.getElementById('displayMargin').innerText = margin.toFixed(1) + '%';
}

// Input events
document.addEventListener('input', function (e) {
    if (
        e.target.classList.contains('quantity-input') ||
        e.target.classList.contains('unit-price-input') ||
        e.target.id === 'marketPurchases' ||
        e.target.id === 'otherExpenses'
    ) {
        calculateTotals();
    }
});

// Select change event
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('item-select')) {
        const row = e.target.closest('.item-row');
        const selected = e.target.options[e.target.selectedIndex];

        if (selected.value) {
            const price = selected.dataset.price || 0;
            row.querySelector('.unit-price-input').value = price;
        }

        calculateTotals();
    }
});

// Add new item row
document.getElementById('addItem').addEventListener('click', function () {
    const container = document.getElementById('itemsContainer');
    const firstRow = container.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);

    // Reset values - QTY starts at 0
    newRow.querySelectorAll('input').forEach(input => {
        if (input.classList.contains('quantity-input')) {
            input.value = '0';
        } else if (input.classList.contains('unit-price-input')) {
            input.value = '';
        } else if (input.classList.contains('item-total')) {
            input.value = 'Tsh0.00';
        }
    });

    newRow.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });

    // Update names properly
    newRow.querySelectorAll('input, select').forEach(el => {
        if (el.name) {
            el.name = el.name.replace(/items\\[\\d+\\]/, `items[${itemCount}]`);
        }
    });

    // Enable remove button
    const removeBtn = newRow.querySelector('.remove-item');
    removeBtn.disabled = false;

    removeBtn.onclick = function () {
        newRow.remove();
        calculateTotals();
    };

    container.appendChild(newRow);
    itemCount++;

    calculateTotals();
});

// Remove existing rows
document.querySelectorAll('.remove-item').forEach(btn => {
    btn.onclick = function () {
        btn.closest('.item-row').remove();
        calculateTotals();
    };
});

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    try {
        initDatePicker();
        calculateTotals();
        console.log('Page initialized successfully');
    } catch (error) {
        console.error('Initialization error:', error);
    }
});
</script>
@endpush