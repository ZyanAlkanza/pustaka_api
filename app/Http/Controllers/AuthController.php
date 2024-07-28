<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [
            'username'  => 'required|min:3',
            'email'     => 'required|email|unique:users,email',
            'address'   => 'required',
            'gender'    => 'required',
            'phone'     => 'required',
            'password'  => 'required|min:8|confirmed',
        ],
        [
            'username.required'  => 'This field is required',
            'email.required'     => 'This field is required',
            'email.email'        => 'Your email is invalid',
            'email.unique'       => 'Your email is already taken',
            'address.required'   => 'This field is required',
            'gender.required'    => 'This field is required',
            'phone.required'     => 'This field is required',
            'password.required'  => 'This field is required',
            'password.min'       => 'Password must be 8 or more characters',
            'password.confirmed' => 'Your password doesnt match'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => false,
                'message'   => 'Validasi Gagal',
                'data'      => $validator->errors(),
            ], 401);
        }

        $data = User::create([
            'username'  => $request->username,
            'email'     => $request->email,
            'address'   => $request->address,
            'join_date' => \Carbon\Carbon::today()->toDateString(),
            'gender'    => $request->gender,
            'phone'     => $request->phone,
            'role'      => 3,
            'password'  => Hash::make($request->password),
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Registrasi Berhasil',
            'data'      => $data
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [
            'email'    => 'required',
            'password' => 'required',
        ],
        [
            'email.required'    => 'This field is required',
            'password.required' => 'This field is required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'data'    => $validator->errors(),
            ], 401);
        }

        if(Auth::attempt($request->only(['email', 'password']))){
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            return response()->json([
                'status'  => true,
                'message' => 'Login Berhasil',
                'data'    => $user,
                'token'   => $token
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Login Gagal',
        ], 401);
    }

    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'  => 'required|email'
        ],[
            'email.required' => 'This field is required',
            'email.email'    => 'This field is invalid'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'data'    => $validator->errors()
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'status'  => false,
                'message' => 'Email Tidak Terdaftar'
            ], 404);
        }

        $token = random_int(100000, 999999);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        $to      = $request->email;
        $subject = 'Reset Password';
        $message = 'This is your recovery code '.$token;

        mail($to, $subject, $message);

        return response()->json([
            'status'  => true,
            'message' => 'Kode pemulihan berhasil dikirim',
        ], 200);
    }

    public function recovery(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'token'  => 'required|numeric',
            'email'  => 'required|email'
        ],[
            'token.required' => 'This field is required',
            'token.numeric'   => 'This field is not number',
            'email.required' => 'This field is required',
            'email.email'    => 'This field is invalid'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'data'    => $validator->errors()
            ], 401);
        }

        $user = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'status'  => false,
                'message' => 'Email Tidak Terdaftar'
            ], 404);
        }

        if($user->token != $request->token){
            return response()->json([
                'status'  => false,
                'message' => 'Kode Pemulihan Salah'
            ], 404);    
        }

        return response()->json([
            'status'  => true,
            'message' => 'Kode Pemulihan Benar',
        ], 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Logout Berhasil',
        ], 200);
    }
}
