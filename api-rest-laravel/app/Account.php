<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    
    // Relación de uno a muchos
    public function transactions(){
        return $this->hasMany('App\Transaction');
    }
    
    // Relación de uno a muchos inversa (muchos a uno)
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
