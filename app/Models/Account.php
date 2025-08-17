<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['user_id','name', 'type', 'balance'];

    public function user(){
        return $this->belongsTo(Account::class);
    }

    public function transactions(){
        return $this->hasMany(Account::class);
    }
}
