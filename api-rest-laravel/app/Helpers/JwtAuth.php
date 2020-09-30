<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{
    
    public $key;
    
    public function __construct() {
        $this->key = 'J0bS1ty2O2O-0998626888';
    }
    
    public function signup($email, $password, $getToken = null){
        
        // Look for if user exists with their credentials
        $user = User::where([
                'email'     => $email,
                'password'  => $password
        ])->first();
        

        // Check if these credentials are correct (object)
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }

        // Generate token with the data of the identificated user
        if($signup){
            
            $token = array(
              'sub'         => $user -> id,
              'email'       => $user -> email,
              'name'        => $user -> name,
              'surname'     => $user -> surname,
              'iat'         => time(),
              'exp'         => time() + (7*27*60*60)
            );
            
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            
            // Return the decoded data or the token, based on a parameter
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }
            
        }else{
            
            $data = array(
                'status'    => 'error',
                'message'   => 'Login is not correct'
            );
        }

        return $data;
    }
    
    public function checkToken($jwt, $getIdentity = false){
        
        $auth = false;
        
        try{
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        } catch(\DomainException $e){
            $auth = false;
        }
        
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        
        if($getIdentity){
            return $decoded;
        }
        
        return $auth;
        
    }
}