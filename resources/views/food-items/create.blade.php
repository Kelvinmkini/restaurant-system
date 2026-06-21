@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="bi bi-database-add me-2 text-primary"></i>Add Food & Drink Items</h4>
                    <p class="text-muted mb-0 small">Add items to database for use in sales</p>
                </div>
                <a href="{{ route('food-items.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list me-1"></i>View All Items
                </a>
            </div>
            <div class="card-body p-4">
                
                <form action="{{ route('food-items.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Item Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Coca Cola">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category...</option>
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                                <option value="drinks">Drinks</option>
                                <option value="dessert">Dessert</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Menu Price (Tsh)</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cost Price (Tsh)</label>
                            <input type="number" name="cost_price" class="form-control" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-save me-2"></i>Save Item to Database
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection