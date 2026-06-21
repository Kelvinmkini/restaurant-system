@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Item</h4>
                    <p class="text-muted mb-0 small">Update item details</p>
                </div>
                <a href="{{ route('food-items.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body p-4">
                
                <form action="{{ route('food-items.update', $foodItem) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Item Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $foodItem->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="breakfast" {{ $foodItem->category == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                <option value="lunch" {{ $foodItem->category == 'lunch' ? 'selected' : '' }}>Lunch</option>
                                <option value="dinner" {{ $foodItem->category == 'dinner' ? 'selected' : '' }}>Dinner</option>
                                <option value="drinks" {{ $foodItem->category == 'drinks' ? 'selected' : '' }}>Drinks</option>
                                <option value="dessert" {{ $foodItem->category == 'dessert' ? 'selected' : '' }}>Dessert</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Menu Price ($)</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                   step="0.01" min="0" value="{{ old('price', $foodItem->price) }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cost Price ($)</label>
                            <input type="number" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" 
                                   step="0.01" min="0" value="{{ old('cost_price', $foodItem->cost_price) }}" required>
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ $foodItem->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$foodItem->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning btn-lg w-100">
                                <i class="bi bi-check-lg me-2"></i>Update Item
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection