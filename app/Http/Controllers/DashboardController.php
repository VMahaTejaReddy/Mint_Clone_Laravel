<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Goal;
use App\Models\Transaction;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function spendingByCategory(Request $request)
{
    $user = JWTAuth::parseToken()->authenticate();

    $spendingData = Transaction::whereIn('account_id', function($query) use ($user) {
            $query->select('id')->from('accounts')->where('user_id', $user->id);
        })
        ->join('categories', 'transactions.category_id', '=', 'categories.id')
        ->select('categories.name as category', DB::raw('SUM(transactions.amount) as total'))
        ->groupBy('categories.name')
        ->orderBy('total', 'desc')
        ->get();

    return response()->json($spendingData);
}
}
