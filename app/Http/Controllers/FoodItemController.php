<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FoodItem;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function index()
    {
        $items = FoodItem::with('category')->orderBy('name')->get();
        return view('food-items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        if ($categories->isEmpty()) {
            return redirect()->route('categories.create')
                ->with('warning', 'Please create at least one category before adding food items.');
        }
        
        return view('food-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        FoodItem::create($validated);

        return redirect()->route('food-items.index')
            ->with('success', 'Food item created successfully.');
    }

    public function show(FoodItem $foodItem)
    {
        return view('food-items.show', compact('foodItem'));
    }

    public function edit(FoodItem $foodItem)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('food-items.edit', compact('foodItem', 'categories'));
    }

    public function update(Request $request, FoodItem $foodItem)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $foodItem->update($validated);

        return redirect()->route('food-items.index')
            ->with('success', 'Food item updated successfully.');
    }

    public function destroy(FoodItem $foodItem)
    {
        if ($foodItem->saleItems()->count() > 0) {
            return back()->with('error', 'Cannot delete food item that has sales records.');
        }

        $foodItem->delete();

        return redirect()->route('food-items.index')
            ->with('success', 'Food item deleted successfully.');
    }

    // Toggle status (active/inactive)
    public function toggleStatus(FoodItem $foodItem)
    {
        $foodItem->is_active = !$foodItem->is_active;
        $foodItem->save();

        $status = $foodItem->is_active ? 'activated' : 'deactivated';

        return redirect()->route('food-items.index')
            ->with('success', "Food item {$status} successfully.");
    }
}