<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Make sure to add user_id and type to the fillable array
    protected $fillable = ['user_id', 'account_id', 'category_id', 'description', 'amount', 'date', 'type'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
