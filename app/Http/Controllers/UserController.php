<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::where('role', 3);

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('username', 'LIKE', "%{$search}%");
        }

        $data = $query->paginate(10);

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

    public function usersData()
    {
        $data = User::where('role', 3)->orderBy('username')->get();
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditampilkan',
            'data' => $data,
        ], 200);
    }

    public function userEdit(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'username'  => 'required|min:3',
            'email'     => ['required','email',Rule::unique('users')->ignore($request->id)],
            'address'   => 'required',
            'gender'    => 'required',
            'phone'     => 'required',
            'image'     => 'image|mimes:jpg,jpeg,png|max:2048'
        ],
        [
            'username.required'  => 'This field is required',
            'email.required'     => 'This field is required',
            'email.email'        => 'Your email is invalid',
            'email.unique'       => 'Your email is already taken',
            'address.required'   => 'This field is required',
            'gender.required'    => 'This field is required',
            'phone.required'     => 'This field is required',
            'image.mimes'        => 'File must be JPEG, JPG or PNG',
            'image.max'          => 'File size maximum 2Mb'


        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => false,
                'message'   => 'Validasi Gagal',
                'data'      => $validator->errors(),
            ], 401);
        }

        $user = User::findOrFail($request->id);

        if($request->hasFile('image')){
            if($user->image){
                Storage::disk('public')->delete('profile/'. $user->image);
            }
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('profile', $file, $fileName);
        }else{
            $fileName = $user->image;
        }

        $data = $user->update([
            'username'  => $request->username,
            'email'     => $request->email,
            'address'   => $request->address,
            'gender'    => $request->gender,
            'phone'     => $request->phone,
            'image'     => $fileName ?? null
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Data Berhasil Diupdate',
        ], 200);
    }

    public function passwordEdit(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'password'              => 'required',
            'new_password'          => 'required|min:8|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'required'
        ],[
            'password.required'              => 'This field is required',
            'new_password.required'          => 'This field is required',
            'new_password.min'               => 'The password must be 8 or more characters',
            'new_password.required_with'     => '',
            'new_password.same'              => 'Your password doesnt match',
            'password_confirmation.required' => 'This field is required'
        ]); 

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'data'    => $validator->errors(),
            ], 401 );
        }

        $user = User::where('id', $request->id)->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'Ubah Password Berhasil',
            ], 200);
        } else {
            return response()->json([
                'status'   => false,
                'message'  => 'Password Anda Salah',
            ], 401);
        }
    }
}
