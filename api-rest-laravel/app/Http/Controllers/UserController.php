<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\Account;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Test Action! USER-CONTROLLER";
    }
    
    public function register(Request $request){
        
        /*$name = $request->input("name");
        $surname = $request->input("surname");
        return "Register user action $name $surname";*/
        
        // Collect user data for post
        $json = $request->input('json', null);
        $params = json_decode($json); // object
        $params_array = json_decode($json, true); // array
        
        if(!empty($params) && !empty($params_array)){
            // Clean data
            $params_array = array_map('trim', $params_array);

            // Validate data
            $validate = \Validator::make($params_array, [
                'name'          => 'required|alpha',
                'surname'       => 'required|alpha',
                'email'         => 'required|email|unique:users',
                'password'      => 'required'
            ]);
            if($validate->fails()){
                // Validation fails
                $data = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'The user has not been created.',
                    'errors'  => $validate->errors()
                );
            }else{
                
                // Validation 0k
                
                // Encrypt pass
                $pwd = hash('sha256', $params->password);

                // Create user
                $user = new User();
                $user -> name = $params_array['name'];
                $user -> surname = $params_array['surname'];
                $user -> email = $params_array['email'];
                $user -> password = $pwd;
                $user -> email = $params_array['email'];
                $user -> role = 'ROLE_USER';
                
                // Save user
                $user->save();
                
                // and save account bank
                $account = new Account();
                $account->user_id = $user->id;
                $account->number_account = str_random(10);
                $account->currency = "USD";
                $account->amount = 0;
                $account->save();
                
                $data = array(
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => 'The user has been created.',
                    'user'    => $user
                );
            }
        }else{
            $data = array(
                'status'  => 'error',
                'code'    => 404,
                'message' => 'The data sent is not correct.'
            );
        }
        
        
        
        
        
        
        return response()->json($data, $data['code']);
    }
    
    public function login(Request $request){
        
        $jwtAuth = new \JwtAuth();
        
        // To receive data via POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        
        // Validate these data
        $validate = \Validator::make($params_array, [
                'email'         => 'required|email',
                'password'      => 'required'
            ]);
        if($validate->fails()){
            // Validation fails
            $signup = array(
                'status'  => 'error',
                'code'    => 404,
                'message' => 'The user has not been able to login',
                'errors'  => $validate->errors()
            );
        }else {
        
            //  Encrypt pass
            $pwd = hash('sha256', $params->password);

            // Return token or data
            $signup = $jwtAuth->signup($params->email, $pwd);
            if(!empty($params->gettoken)){
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }
        
        return response()->json($signup, 200);
    }
    
    public function update(Request $request) {
        
        // CHeck if the user is indentified
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        
        // To receive data via POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if($checkToken && !empty($params_array)){
            
            // Update user
            
            // Obtain the identified user
            $user = $jwtAuth->checkToken($token, true);
            
            // Validate data
            $validate = \Validator::make($params_array, [
                'name'          => 'required|alpha',
                'surname'       => 'required|alpha',
                'email'         => 'required|email|unique:users'.$user->sub
            ]);
            
            // Remove fields that we don't want update
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            
            // Update user in bbdd
            $user_update = User::where('id', $user->sub)->update($params_array);
            
            // Return an array with the result
            $data = array(
                'code'     => 200,
                'status'   => 'success',
                'user'     => $user,
                'changes'  => $params_array
            );
        }
        else{
            $data = array(
                'code'     => 400,
                'status'   => 'error',
                'message'  => 'The user is not identified'
            );
        }
        
        return response()->json($data, $data['code']);
    }
    
    public function upload(Request $request){
        
        // To receive petition data
        $image = $request->file('file0');
        
        // Validate image
        $validate = \Validator::make($request->all(), [
            'file0'   => 'required|image|mimes:jpg,jpeg,png,gif',
            
        ]);
        
        // Save image
        if(!$image || $validate->fails()){
            $data = array(
                'code'     => 400,
                'status'   => 'error',
                'message'  => 'Error uploading image'
            );
        }else{
            
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            
            $data = array(
                'code'     => 200,
                'status'   => 'success',
                'image'    => $image_name
            );
        
        }
        
        
        
        
        return response()->json($data, $data['code']);
    }
    
    public function getImage($filename){
        
        $isset = \Storage::disk('users')->exists($filename);
        
        if($isset){
        
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        }else{
            $data = array(
                'code'     => 404,
                'status'   => 'error',
                'message'  => 'Image do not exists'
            );
            
            return response()->json($data, $data['code']);
        }
        
    }
    
    public function detail($id){
        
        $user = User::find($id);
        
        if(is_object($user)){
            $data = array(
                'code'     => 200,
                'status'   => 'success',
                'user'    => $user
            );
        }else{
            $data = array(
                'code'     => 404,
                'status'   => 'error',
                'message'  => 'User do not exists'
            );
        }
        
        return response()->json($data, $data['code']);
    }
}
