<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Budget;
// use App\Models\Category;
// use Illuminate\Support\Facades\Auth;

// class BudgetController extends Controller
// {
//     public function index()
//     {
//         // Eager load the category name for easier display on the frontend
//         return response()->json(Budget::with('category')->where('user_id', Auth::id())->get());
//     }

    

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'category_id' => 'required|exists:categories,id',
//             'amount' => 'numeric|required'
//         ]);
        
//         $validated['user_id'] = Auth::id();
//         // $validated['category_id'] = $validated['category_id']; // Ensure category_id is set correctly

//         $budget = Budget::create($validated);
//         return response()->json($budget->load('category'), 201); // Return with category info
//     }

//     // ... Implement show, update, destroy with the same security pattern ...
// }
// 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
{
    return response()->json(
        Budget::with('category')->where('user_id', Auth::id())->get()
    );
}

    public function display(){
        $categories = Category::all();
        return view('Budgets', compact('categories'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'category_id' => 'required|exists:categories,id',
    //         'amount' => 'required|numeric',
    //     ]);

    //     $validated['user_id'] = Auth::id();
    //     $budget = Budget::create($validated);
    //     return response()->json($budget, 201);
    // }

    public function store(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'amount' => 'required|numeric',
    ]);

    $validated['user_id'] = Auth::id();
    $budget = Budget::create($validated);

    // Eager load category before returning
    return response()->json($budget->load('category'), 201);
}


    public function show($id)
    {
        $budget = Budget::with('category')->find($id);
        if (!$budget) {
            return response()->json(['message' => 'Budget not found'], 404);
        }
        return response()->json($budget);
    }

    public function update(Request $request, $id)
    {
        $budget = Budget::find($id);
        if (!$budget) {
            return response()->json(['message' => 'Budget not found'], 404);
        }

        $validated = $request->validate([
            'category_id' => 'exists:categories,id',
            'amount' => 'numeric',
        ]);

        $budget->update($validated);
        return response()->json($budget);
    }

    public function destroy($id)
    {
        $budget = Budget::find($id);
        if (!$budget) {
            return response()->json(['message' => 'Budget not found'], 404);
        }
        $budget->delete();
        return response()->json(['message' => 'Budget deleted successfully']);
    }
}
