@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Category</h2>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <input type="color" name="color" class="form-control" value="{{ old('color', $category->color) }}">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection