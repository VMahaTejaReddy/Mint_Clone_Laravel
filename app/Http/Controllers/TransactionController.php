<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
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
    try {
        // Get all account IDs that belong to the logged-in user
        $accountIds = Auth::user()->accounts()->pluck('id');

        // Fetch only transactions that belong to those accounts
        $transactions = Transaction::with(['category', 'account'])
            ->whereIn('account_id', $accountIds)
            ->get();

        return response()->json($transactions);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



    public function display(){
        $categories = Category::all();
        $accounts=Account::where('user_id', Auth::id())->get();
        return view('Transactions', compact('categories', 'accounts'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    if (!Account::where('id', $request->account_id)->where('user_id', Auth::id())->exists()) {
    return response()->json(['error' => 'Invalid account'], 403);
}

    $validate = $request->validate([
        'account_id' => 'required|exists:accounts,id',
        'category_id' => 'required|exists:categories,id',
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'date' => 'required|date'
    ]);
    $validate['user_id'] = Auth::id();
    $transaction = Transaction::create($validate);

    // reload with relations
    $transaction->load(['category', 'account']);

    return response()->json($transaction, 201);
}
    /**
     * Display the specified resource.
     */
  public function show($id)
{
    $accountIds = Auth::user()->accounts()->pluck('id');

    $transaction = Transaction::with(['category', 'account'])
        ->whereIn('account_id', $accountIds)
        ->where('id', $id)
        ->first();

    if (!$transaction) {
        return response()->json(['message' => 'Transaction not found'], 404);
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
        if ($transaction->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
        if ($transaction->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $transaction->delete();
        return response()->json(['message'=>'Transaction deleted']);
    }
}