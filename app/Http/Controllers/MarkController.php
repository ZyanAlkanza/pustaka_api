<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use Illuminate\Http\Request;

class MarkController extends Controller
{
    public function index($id)
    {
        $data = Mark::where('user_id', $id)->with('user', 'book')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditampilkan',
            'data'    => $data
        ], 200);
    }

    public function addMark(Request $request)
    {

        $checkMark = Mark::where('user_id', $request->user_id)
                            ->where('book_id', $request->book_id)
                            ->first();
        
        if($checkMark){
            return response()->json([
                'status'  => false,
                'message' => 'Data Sudah Ada',
            ], 409);
        }

        $data = Mark::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditambah',
            'data'    => $data
        ], 200);
    }
}
