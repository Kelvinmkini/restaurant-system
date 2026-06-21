<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FoodItemController extends Controller
{
    public function index(): View
    {
        $items = FoodItem::orderBy('category')->orderBy('name')->paginate(20);
        return view('food-items.index', compact('items'));
    }

    public function create(): View
    {
        return view('food-items.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:food_items,name',
            'category' => 'required|in:breakfast,lunch,dinner,drinks,dessert',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
        ]);

        FoodItem::create($validated);
        return redirect()->route('food-items.index')->with('success', 'Item "' . $validated['name'] . '" added successfully!');
    }

    // NEW: Show edit form
    public function edit(FoodItem $foodItem): View
    {
        return view('food-items.edit', compact('foodItem'));
    }

    // NEW: Update item
    public function update(Request $request, FoodItem $foodItem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:food_items,name,' . $foodItem->id,
            'category' => 'required|in:breakfast,lunch,dinner,drinks,dessert',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $foodItem->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('food-items.index')->with('success', 'Item "' . $validated['name'] . '" updated successfully!');
    }

    // NEW: Delete item
    public function destroy(FoodItem $foodItem): RedirectResponse
    {
        $name = $foodItem->name;
        
        // Check if item has sales before deleting
        if ($foodItem->saleItems()->count() > 0) {
            return redirect()->route('food-items.index')
                ->with('error', 'Cannot delete "' . $name . '" because it has sales records. Deactivate it instead.');
        }

        $foodItem->delete();
        return redirect()->route('food-items.index')->with('success', 'Item "' . $name . '" deleted successfully!');
    }
}