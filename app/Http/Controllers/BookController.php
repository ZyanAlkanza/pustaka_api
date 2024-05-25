<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index()
    {
        $data = Book::all();
        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditampilkan',
            'data'    => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'title'       => 'required|unique:books,title',
            'author'      => 'required',
            'book_detail' => 'required',
        ],
        [
            'title.required'  => 'This field is required',
            'title.unique'    => 'This title already exists',
            'author.required' => 'This field is required',
            'book_detail'     => 'This field is required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'error'   => $validator->errors(),
            ], 401);
        }

        $data = Book::create([
            'title'       => $request->title,
            'author'      => $request->author,
            'status'      => 1,
            'book_detail' => $request->book_detail,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditambahkan',
            'data'    => $data
        ], 200);
    }


    public function show($id)
    {
        try{
            $data = Book::findOrFail($id);
            return response()->json([
                'status'  => true,
                'message' => 'Data Berhasil Ditampilkan',
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


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),
        [
            'title'       => ['required', Rule::unique('books')->ignore($id)],
            'author'      => 'required',
            'book_detail' => 'required'
        ],
        [
            'title.required'       => 'This field is required',
            'title.unique'         => 'This title already exists',
            'author.required'      => 'This field is required',
            'book_detail.required' => 'This field is required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'error'   => $validator->errors(),
            ], 401);
        }

        $data = Book::where('id', $id)->update([
            'title'       => $request->title,
            'author'      => $request->author,
            'book_detail' => $request->book_detail
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Diupdate',
            'data'    => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $data = Book::findOrFail($id);
            $data->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Data Berhasil Dihapus',
                'data'    => $data 
            ], 200);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Data Gagal Dihapus',
                'error'    => $e->getMessage(), 
            ], 404);
        }
    }
}
