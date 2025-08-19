<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Transaction::all(),200);
    }

    public function display(){
        $categories = Category::all();
        return view('Transactions', compact('categories'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date'
        ]);
        $validate['user_id'] = Auth::id(); // Securely set user_id
        $transaction = Transaction::create($validate);
        return response()->json($transaction, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $transaction = Transaction::all();
        if (!$transaction){
            return response()->json(['message'=>'Transaction not found'], 404);
        }
        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction){
            return response()->json(['message'=>'Transaction not found'], 404);
        }

        $validate = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:100',
            'amount' => 'required|numeric',
            'date' => 'required|date'
        ]);

        $transaction->update($validate);
        return response()->json($transaction,200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction){
            return response()->json(['message'=>'Transaction not found'], 404);
        };
        $transaction->delete();
        return response()->json(['message'=>'Transaction deleted']);
    }
}
