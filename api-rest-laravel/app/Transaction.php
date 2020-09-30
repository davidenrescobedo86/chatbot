<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    
    // Relación de uno a muchos inversa (muchos a uno)
    public function account(){
        return $this->belongsTo('App\Account', 'account_id');
    }
}
