@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-menu-button-wide me-2 text-primary"></i>Food & Drinks Menu</h4>
        <a href="{{ route('food-items.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add New Item
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Menu Price</th>
                        <th>Cost Price</th>
                        <th>Margin</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td class="fw-bold">{{ $item->name }}</td>
                        <td>
                            <span class="badge bg-{{ 
                                $item->category == 'drinks' ? 'info' : 
                                ($item->category == 'dessert' ? 'warning' : 
                                ($item->category == 'breakfast' ? 'primary' : 
                                ($item->category == 'lunch' ? 'success' : 'secondary'))) 
                            }}">
                                {{ ucfirst($item->category) }}
                            </span>
                        </td>
                        <td>Tsh{{ number_format($item->price, 2) }}</td>
                        <td>Tsh{{ number_format($item->cost_price, 2) }}</td>
                        <td>
                            @php $margin = $item->price > 0 ? (($item->price - $item->cost_price) / $item->price) * 100 : 0; @endphp
                            <span class="badge bg-{{ $margin > 40 ? 'success' : ($margin > 20 ? 'warning' : 'danger') }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('food-items.edit', $item) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection