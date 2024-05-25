<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
