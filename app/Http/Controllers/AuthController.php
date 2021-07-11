<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon;

class AuthController extends ResponseController
{
    public function userRegitration(Request $request)
    {
        $rules = array(
            'name'=> 'required|min:3',
            'password'=> 'required',
            'email' => 'required|email|unique:users'
        );
        $validation = Validator::make($request->all(),$rules);
        if ($validation->fails())  {
            $this->status = false;
            $this->errors = "error";
            $this->message = $validation->errors()->toArray();
            $this->data = json_decode('{}');
            return $this->sendResult();
            // return response()->json($validation->errors()->toArray());
        }
        if(User::where('email',$request->email)->exists()){
            $this->status = false;
            $this->errors = "Email Id exists";
            $this->message = "";
            $this->data = json_decode('{}');
        }else{
            $filename = "";
            if($file = $request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = $file->getClientOriginalName();
                $path = public_path() . '/uploads/profile';
                $file->move($path, $file->getClientOriginalName());
            }
            $request['password'] = Hash::make($request->password);
            $user = User::create($request->all());
            $this->status = true;
            $this->errors = [];
            $this->message = "User Registration SuccessFully";
            $this->data = $user;
        }
        return $this->sendResult();
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
 
     public function userLogin(Request $request)
     {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );
        $validation = Validator::make($request->all(),$rules);
        if ($validation->fails())  {
            $this->status = false;
            $this->errors = [];
            $this->message = $validation->errors()->toArray();
            $this->data = null;
            return $this->sendResult();
        }
        $credentials = request(['email', 'password']);
        if ($request->email && $request->password) {
            $credentials = $request->only('email', 'password');
            $user = User::where('email',$request->email)->first();

             if($user)
             {
                 if(Hash::check($request->password, $user->password))
                 {
                     try {
                         if (! $token = JWTAuth::attempt($credentials, ['exp' => Carbon\Carbon::now()->addMonths(12)->timestamp]) )
                         {
                             return response()->json(['message' => '', 'status' => false , 'data' => json_decode('{}'), 'errors' => 'Please enter correct password'], 400);
                         }
                     } catch (JWTException $e ) {
                         return response()->json(['message' => '', 'status' => false , 'data' => json_decode('{}'),'errors' => 'could_not_create_token'], 500);
                     }
 
                     $user->api_token = $token;
 
                     $this->status = true;
                     $this->errors = "User Login Successfully";
                     $this->message = [];
                     $this->data = $user;
                     
                 }else {
                     $this->status = false;
                     $this->errors = [];
                     $this->message = ['password'=>['Inccorect Password']];
                 }
             }else {
                 $this->status = false;
                 $this->errors =[];
                 $this->message =['email'=>['Email Not Found']];
             }
         }
         else
         {
             $this->status = false;
             $this->errors = "Missing Parameter";
             $this->message =[];
         }
 
         return $this->sendResult();
     }
}
