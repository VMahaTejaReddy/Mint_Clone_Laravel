<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     * We'll return accounts with their transactions nested inside.
     */
    public function index()
    {
        try {
            // Get all accounts for the logged-in user and eager load their transactions,
            // also loading the category for each transaction.
            $accounts = Auth::user()->accounts()->with(['transactions.category'])->get();
            return response()->json($accounts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function display()
    {
        $categories = Category::all();
        $accounts = Account::where('user_id', Auth::id())->get();
        return view('Transactions', compact('categories', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ensure the account belongs to the authenticated user and get the account model
        $account = Account::where('id', $request->account_id)->where('user_id', Auth::id())->first();

        if (!$account) {
            return response()->json(['error' => 'Invalid account'], 403);
        }

        $validate = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'type' => 'required|string|in:income,expense',
        ]);

        // --- NEW: Balance Check Logic ---
        if ($validate['type'] === 'expense' && $account->balance < $validate['amount']) {
            // Return a specific error response if funds are insufficient
            return response()->json(['message' => 'Insufficient funds for this transaction.'], 422);
        }

        $validate['user_id'] = Auth::id(); // Assign the user ID
        $transaction = Transaction::create($validate);

        // Update account balance
        if ($request->type === 'income') {
            $account->balance += $request->amount;
        } else {
            $account->balance -= $request->amount;
        }
        $account->save();

        // Reload with relations to send back to the frontend
        $transaction->load(['category', 'account']);
        return response()->json($transaction, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['category', 'account'])->where('user_id', Auth::id())->find($id);

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
        $transaction = Transaction::where('user_id', Auth::id())->find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Additional check for account ownership
        if (!Account::where('id', $request->account_id)->where('user_id', Auth::id())->exists()) {
            return response()->json(['error' => 'Invalid account'], 403);
        }
        
        $originalAmount = $transaction->amount;
        $originalType = $transaction->type;
        $account = Account::find($transaction->account_id);

        $validate = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'type' => 'required|string|in:income,expense',
        ]);
        
        // Revert old transaction from balance
        if ($originalType === 'income') {
            $account->balance -= $originalAmount;
        } else {
            $account->balance += $originalAmount;
        }

        $transaction->update($validate);

        // Apply new transaction to balance
        if ($request->type === 'income') {
            $account->balance += $request->amount;
        } else {
            $account->balance -= $request->amount;
        }
        $account->save();

        $transaction->load(['category', 'account']); // return relations
        return response()->json($transaction, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        };
        
        // Adjust account balance before deleting
        $account = Account::find($transaction->account_id);
        if ($transaction->type === 'income') {
            $account->balance -= $transaction->amount;
        } else {
            $account->balance += $transaction->amount;
        }
        $account->save();

        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }
}

