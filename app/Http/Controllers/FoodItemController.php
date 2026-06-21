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
        return redirect()->route('food-items.create')->with('success', 'Item "' . $validated['name'] . '" added to database successfully!');
    }
}