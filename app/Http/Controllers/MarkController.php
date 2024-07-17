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

    // public function addMark(Request $request)
    // {

    //     $checkMark = Mark::where('user_id', $request->user_id)
    //                         ->where('book_id', $request->book_id)
    //                         ->first();
        
    //     if ($checkMark) {
    //         $checkMark->delete();
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Buku berhasil dihapus dari koleksi',
    //         ], 200);
    //     } else {
    //         $data = Mark::create([
    //             'user_id' => $request->user_id,
    //             'book_id' => $request->book_id,
    //         ]);
        
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Buku berhasil ditambah ke koleksi',
    //             'data'    => $data
    //         ], 200);
    //     }
        
    // }

    public function toggleMark(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        // Periksa apakah pengguna sudah memiliki buku dalam koleksi
        $existingMark = Mark::where('user_id', $request->user_id)
                            ->where('book_id', $request->book_id)
                            ->first();

        if ($existingMark) {
            // Hapus buku dari koleksi
            $existingMark->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Buku berhasil dihapus dari koleksi',
            ], 200);
        } else {
            // Tambahkan buku ke koleksi
            $data = Mark::create([
                'user_id' => $request->user_id,
                'book_id' => $request->book_id,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Buku berhasil ditambahkan ke koleksi',
                'data'    => $data
            ], 200);
        }
    }


    public function checkMark(Request $request)
    {
        $isBookMarked = Mark::where('user_id', $request->user_id)
                            ->where('book_id', $request->book_id)
                            ->exists();

        return response()->json([
            'isBookMarked' => $isBookMarked
        ], 200);
    }


}
