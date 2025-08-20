<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Tymon\JWTAuth\Facades\JWTAuth;

class AccountController extends Controller
{
    public function index()
    {
        return response()->json(Account::where('user_id', Auth::id())->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'balance' => 'required|numeric'
        ]);

       //  $user = JWTAuth::parseToken()->authenticate();
        $validated['user_id'] = Auth::id(); // Securely set user_id

        $account = Account::create($validated);
        return response()->json($account, 201);
    }

    public function show($id)
    {
        $account = Account::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return response()->json($account);
    }

    // app/Http/Controllers/AccountController.php

public function edit($id)
{
    $account = Account::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    return view('edit', compact('account')); // This loads the edit.blade.php file
}

    public function update(Request $request, $id)
    {
        $account = Account::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'balance' => 'required|numeric'
        ]);

        $validated['user_id'] = Auth::id();

        $account->update($validated);
        return response()->json($account, 200);
    }

    public function destroy($id)
    {
        $account = Account::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $account->delete();
        return response()->json(['message' => 'Account deleted']);
    }
}