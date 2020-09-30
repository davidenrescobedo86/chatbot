<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Transaction;

class PruebasController extends Controller
{
    public function index(){
        
        $titulo = "Animales";
        
        $animales = ['Perro', 'Gato', 'Tigre'];
        
        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }
    
    public function testORM(){
        echo"<h1>Transactions</h1>";
        echo"<br/>";
        $transactions = Transaction::all();
        foreach ($transactions as $transaction){
            echo "<h2>From: ".$transaction->account->user->name."(".$transaction->account->number_account.")</h2>";
            echo "<h2>To: ".$transaction->destination_account."</h2>";
            echo "<h2>Amount: ".$transaction->amount."</h2>";
            echo "<hr>";
        }
        
        echo"<h1>Accounts</h1>";
        echo"<br/>";
        $accounts = Account::all();
        foreach ($accounts as $account){
            echo "<h2>Owner: ".$account->user->name." ".$account->user->surname."</h2>";
            echo "<h2>Amount: ".$account->amount."</h2>";
            echo "<hr>";
        }
        
        die();
    }
}
