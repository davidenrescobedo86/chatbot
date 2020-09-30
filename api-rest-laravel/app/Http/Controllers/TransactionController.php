<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Account;
use App\User;
use App\Transaction;

class TransactionController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'getTransactionsByAccount', 'convert']]);
    }

    public function index(Request $request) {

        $accounts = Transaction::all();
        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'accounts' => $accounts
        ]);
    }

    public function getTransactionsByAccount($account_id) {
        $transactions = Transaction::where('account_id', $account_id)->get();

        $data = [
            'code' => 200,
            'status' => 'success',
            'transactions' => $transactions
        ];


        return response()->json($data, $data['code']);
    }


    public function convert(Request $request) {
        // set API Endpoint and API key 
        $endpoint = 'latest';
        $access_key = 'fc7707a9c9205e823eca9a7ba31968ac';

        // Initialize CURL:
        $ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $exchangeRates = json_decode($json, true);

        // Access the exchange rate values, e.g. GBP:
        //echo $exchangeRates['rates']['GBP'];
        //echo "<br/>";
        //echo $exchangeRates['rates']['USD'];
        //echo "<br/>";
        //echo $exchangeRates['rates']['EUR'];
        
        $data = [
            'EUR' => $exchangeRates['rates']['EUR'],
            'USD' => $exchangeRates['rates']['USD'],
            'GPB' => $exchangeRates['rates']['GBP'],
            'LAK' => $exchangeRates['rates']['LAK'],
            'MUR' => $exchangeRates['rates']['MUR'],
        ];
        
        return response($data);
    }

/*
    public function convert(Request $request) {
        
        
        $api_key = $request->input('api_key');
        $currency1 = $request->input('from');
        $currency2 = $request->input('to');
        $amount = $request->input('amount');
        
        $data_v = file_get_contents('https://www.amdoren.com/api/currency.php?api_key='.$api_key.'&from='.$currency1.'&to='.$currency2.'&amount='.$amount);
        
        
        
        
        $parsed_json = json_decode($data_v);
        
        $data = [
            'error' => $parsed_json->error,
            'error_message' => $parsed_json->error_message,
            'amount' => $parsed_json->amount
        ]; 
        
        
        return response($data);

    }
     
     */
    
    

}
