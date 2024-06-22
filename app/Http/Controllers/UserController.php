<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $data = User::where('role', 3)->paginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditampilkan',
            'data' => $data,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'username'  => 'required|min:3',
            'email'     => 'required|email|unique:users,email',
            'address'   => 'required',
            'gender'    => 'required',
            'phone'     => 'required',
        ],
        [
            'username.required'  => 'This field is required',
            'email.required'     => 'This field is required',
            'email.email'        => 'Your email is invalid',
            'email.unique'       => 'Your email is already taken',
            'address.required'   => 'This field is required',
            'gender.required'    => 'This field is required',
            'phone.required'     => 'This field is required',
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
            'password'  => Hash::make('123'),
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Data Berhasil Ditambahkan',
            'data'      => $data
        ], 200);
    }

    public function show($id)
    {
        try{
            $data = User::findOrFail($id);
            $date = $data->join_date;
            $dateFormat = Carbon::parse($date)->locale('id')->isoFormat('DD MMMM YYYY');
            return response()->json([
                'status'  => true,
                'message' => 'Data Berhasil Ditampilkan',
                'data'    => [
                    'data' => $data,
                    'date_format' => $dateFormat]
            ], 200);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Data Tidak Ditemukan',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),
        [
            'username'  => 'required|min:3',
            'email'     => ['required','email',Rule::unique('users')->ignore($id)],
            'address'   => 'required',
            'gender'    => 'required',
            'phone'     => 'required',
        ],
        [
            'username.required'  => 'This field is required',
            'email.required'     => 'This field is required',
            'email.email'        => 'Your email is invalid',
            'email.unique'       => 'Your email is already taken',
            'address.required'   => 'This field is required',
            'gender.required'    => 'This field is required',
            'phone.required'     => 'This field is required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => false,
                'message'   => 'Validasi Gagal',
                'data'      => $validator->errors(),
            ], 401);
        }

        User::where('id', $id)->update([
            'username'  => $request->username,
            'email'     => $request->email,
            'address'   => $request->address,
            'gender'    => $request->gender,
            'phone'     => $request->phone,
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Data Berhasil Diupdate',
        ], 200);
    }

    public function destroy($id)
    {
        try{
            $data = User::findOrFail($id);
            $data->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Data Berhasil Dihapus',
                'data'    => $data
            ], 200);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Data Tidak Ditemukan',
                'error'   => $e->getMessage()
            ], 404);
        }
    }
}
