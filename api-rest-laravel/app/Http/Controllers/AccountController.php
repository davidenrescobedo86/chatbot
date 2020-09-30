<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Account;
use App\User;
use App\Transaction;

class AccountController extends Controller
{
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getAccountByUser', 'deposit', 'extract']]);
    }
    
    public function index(Request $request){
        
        $accounts = Account::all();
        return response()->json([
            'code'     => 200,
            'status'   => 'success',
            'accounts' => $accounts
        ]);
    }
    
    public function show($id){
        $account = Account::find($id);
        
        if(is_object($account)){
            $data = array(
                'code'     => 200,
                'status'   => 'success',
                'account' => $account
            );
        }else{
            $data = array(
                'code'     => 404,
                'status'   => 'error',
                'message' => 'The account does not exists'
            );
        }
        
        return response()->json($data, $data['code']);
    }
    
    public function getAccountByUser($user_id){
        $account = Account::where('user_id', $user_id)->get();
        
        if(is_object($account)){
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'account' => $account
            ];
        }else{
            $data = [
                'code'     => 404,
                'status'   => 'error',
                'message' => 'The account does not exists'
            ];
        }
        return response()->json($data, $data['code']);
    }
    
    public function store(Request $request){
        
        // To receive data via post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if(!empty($params_array)){
            // Validate data
            $validate = \Validator::make($params_array, [
                'user_id'           =>'required',
                'number_account'    =>'required|unique:accounts',
                'currency'          =>'required',
                'amount'            =>'required|numeric'
            ]);

            // Save account
            if($validate->fails()){
                $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'Error saving account'
                ];
            }else{
                $account = new Account();
                $account->user_id = $params_array['user_id'];
                $account->number_account = $params_array['number_account'];
                $account->currency = $params_array['currency'];
                $account->amount = $params_array['amount'];
                $account->save();

                $data = [
                    'code'      =>200,
                    'status'    =>'success',
                    'account'   =>$account
                ];
            }
        }else{
            $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'You have not sent an account'
                ];
        }
        
        // Return result
        return response()->json($data, $data['code']);
    }
    
    public function update($id, Request $request){
        
        // To receive data via post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if(!empty($params_array)){
            // Validate data
            $validate = \Validator::make($params_array, [
                'currency'          =>'required',
                'amount'            =>'required|numeric'
            ]);

            // Save account
            if($validate->fails()){
                $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'Error updating account'
                ];
            }else{
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['number_account']);
                unset($params_array['created_at']);
                $account = Account::where('id', $id)->update($params_array);
                $data = [
                    'code'      =>200,
                    'status'    =>'success',
                    'account'   =>$account
                ];
            }
        }else{
            $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'You have not sent an account'
                ];
        }
        
        // Return result
        return response()->json($data, $data['code']);
        
    }
    
    public function deposit($id, Request $request){
        $account = Account::find($id);
        
        // To receive data via post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if(!empty($params_array)){
            // Validate data
            $validate = \Validator::make($params_array, [
                'amount'            =>'required|numeric'
            ]);

            // Save account
            if($validate->fails()){
                $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'Error updating amount'
                ];
            }else{
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['number_account']);
                unset($params_array['created_at']);
                unset($params_array['currency']);
                
                
                $how_came = $params_array['amount'];
                
                $new_am = $account->amount + $params_array['amount'];
                
                
                $params_array['amount'] = $new_am;
                
                $account = Account::where('id', $id)->update($params_array);
                
                
                
      
                
                // return de account
                $account = Account::find($id);
                
                
                // saving this transaction
                $transaction = new Transaction();
                $transaction->account_id = $account->id;
                $transaction->amount = $how_came;
                $transaction->what = "deposit";
                $transaction->save();
                
                
                $data = [
                    'code'      =>200,
                    'status'    =>'success',
                    'account'   =>$account
                ];
            }
        }else{
            $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'You have not sent a deposit'
                ];
        }
        
        // Return result
        return response()->json($data, $data['code']);
        
    }
    
    
    public function extract($id, Request $request){
        $account = Account::find($id);
        
        // To receive data via post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if(!empty($params_array)){
            // Validate data
            $validate = \Validator::make($params_array, [
                'amount'            =>'required|numeric'
            ]);

            // Save account
            if($validate->fails()){
                $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'Error updating amount'
                ];
            }else{
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['number_account']);
                unset($params_array['created_at']);
                unset($params_array['currency']);
                
                
                if($params_array['amount']>$account->amount){
                    $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'No puedes extraer esa cantidad'
                ];
                }else{
                    
                    $how_came = $params_array['amount'];
                    
                    $new_am = $account->amount - $params_array['amount'];
                
                
                    $params_array['amount'] = $new_am;

                    $account = Account::where('id', $id)->update($params_array);

                    // return de account
                    $account = Account::find($id);
                    

                    // saving this transaction
                    $transaction = new Transaction();
                    $transaction->account_id = $account->id;
                    $transaction->amount = $how_came;
                    $transaction->what = "extract";
                    $transaction->save();

                    $data = [
                        'code'      =>200,
                        'status'    =>'success',
                        'account'   =>$account
                    ];
                }
                
            }
        }else{
            $data = [
                    'code'      =>400,
                    'status'    =>'error',
                    'message'   =>'You have not sent a deposit'
                ];
        }
        
        // Return result
        return response()->json($data, $data['code']);
        
    }
    
    
}
