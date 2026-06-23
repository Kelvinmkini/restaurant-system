@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Food Item</h2>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('food-items.update', $foodItem) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $foodItem->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $foodItem->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Menu Price (Tsh) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $foodItem->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cost Price (Tsh)</label>
                        <input type="number" step="0.01" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', $foodItem->cost_price) }}">
                        @error('cost_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $foodItem->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Food Item</button>
                <a href="{{ route('food-items.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection