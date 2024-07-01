<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    // public function index()
    // {
    //     $data = Book::paginate(10);
    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Data Berhasil Ditampilkan',
    //         'data'    => $data
    //     ], 200);
    // }

    public function index(Request $request){
        $query = Book::query();

        if($request->has('search')){
            $search = $request->query('search');
            $query->where('title', 'LIKE', "%{$search}%")->orWhere('author', 'LIKE', "%{$search}%");
        }

        $data=$query->paginate(10);

        return response()->json([
            'statur'  => true,
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
            'image'       => 'image|mimes:jpg,jpeg,png|max:1024'
        ],
        [
            'title.required'  => 'This field is required',
            'title.unique'    => 'This title already exists',
            'author.required' => 'This field is required',
            'book_detail'     => 'This field is required',
            'image.mimes'     => 'File must be JPEG, JPG or PNG',
            'image.max'       => 'File size maximum 1Mb'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'error'   => $validator->errors(),
            ], 401);
        }

        if($request->hasFile('image')){
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('covers', $file, $fileName);
        }

        $data = Book::create([
            'title'       => $request->title,
            'author'      => $request->author,
            'status'      => 1,
            'book_detail' => $request->book_detail,
            'image'       => $fileName ?? null
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
            'book_detail' => 'required',
            'book_detail' => 'required',
            'image'       => 'image|mimes:jpg,jpeg,png|max:1024'
        ],
        [
            'title.required'       => 'This field is required',
            'title.unique'         => 'This title already exists',
            'author.required'      => 'This field is required',
            'book_detail.required' => 'This field is required',
            'image.mimes'          => 'File must be JPEG, JPG or PNG',
            'image.max'            => 'File size maximum 1Mb'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'error'   => $validator->errors(),
            ], 401);
        }

        $book = Book::findOrFail($id);

        if($request->hasFile('image')){
            if($book->image){
                Storage::disk('public')->delete('covers/' . $book->image);
            }
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('covers', $file, $fileName);
        }

        $data = $book->update([
            'title'       => $request->title,
            'author'      => $request->author,
            'book_detail' => $request->book_detail,
            'image'       => $fileName ?? null 
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Diupdate',
            'data'    => $data
        ], 200);
    }

    public function destroy($id)
    {
        try{
            $data = Book::findOrFail($id);
            if($data->image){
                Storage::disk('public')->delete('covers/' . $data->image);
            }
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

    public function home()
    {
        $data = Book::all();
        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditampilkan',
            'data'    => $data
        ], 200);
    }

    public function booksData()
    {
        $data = Book::orderBy('title')->get();
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditampilkan',
            'data' => $data,
        ], 200);
    }
}
