@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Food Items</h2>
        <a href="{{ route('food-items.create') }}" class="btn btn-primary">Add Food Item</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Menu Price (Tsh)</th>
                        <th>Cost Price (Tsh)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td class="fw-bold">{{ $item->name }}</td>
                        <td>
                            @if($item->category)
                                <span class="badge category-badge" data-color="{{ $item->category->color ?? '#6c757d' }}">
                                    {{ $item->category->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Uncategorized</span>
                            @endif
                        </td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ number_format($item->cost_price, 2) }}</td>
                        <td>
                            <form action="{{ route('food-items.toggle-status', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                    @if($item->is_active)
                                        <i class="bi bi-check-circle-fill"></i> Active
                                    @else
                                        <i class="bi bi-x-circle"></i> Inactive
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('food-items.edit', $item) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('food-items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No food items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection