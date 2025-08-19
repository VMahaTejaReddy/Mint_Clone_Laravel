<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index()
    {
        return response()->json(Bill::where('user_id', Auth::id())->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric',
            'due_date' => 'required|date'
        ]);

        $validated['user_id'] = Auth::id(); // Securely set user_id

        $bill = Bill::create($validated);
        return response()->json($bill, 201);
    }
    
    
}