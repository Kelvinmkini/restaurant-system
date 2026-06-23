<?php

namespace App\Http\Middleware;

use App\Models\Category;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCategoriesExist
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check kama kuna category active
        if (Category::where('is_active', true)->count() === 0) {
            
            // Kama ni AJAX/API request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please create a category first before managing food items.'
                ], 403);
            }
            
            // Kama ni normal web request
            return redirect()->route('categories.create')
                ->with('warning', 'You need to create at least one category before adding food items.');
        }

        return $next($request);
    }
}