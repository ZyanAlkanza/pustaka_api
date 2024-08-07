<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    // public function index()
    // {
    //     $data = Transaction::with('book', 'user')->get();
    //     foreach ($data as $transaction) {
    //         $transaction->formatted_loan_date = Carbon::parse($transaction->loan_date)->locale('id')->isoFormat('DD MMMM YYYY');
    //         $transaction->formatted_date_of_return = Carbon::parse($transaction->date_of_return)->locale('id')->isoFormat('DD MMMM YYYY');
    //     }
    //     // $data = Transaction::all();
    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Data Berhasil Ditampilkan',
    //         'data'    =>  $data
    //     ], 200);
    // }

    public function index(Request $request)
    {
        $query = Transaction::with('book', 'user')->orderBy('id');
        
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('username', 'LIKE', "%{$search}%");
            });
            $query->orWhereHas('book', function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->paginate(10);
        
        foreach ($data as $transaction) {
            $transaction->format_loan_date = Carbon::parse($transaction->loan_date)->locale('id')->isoFormat('DD MMMM YYYY');
            $transaction->format_date_of_return = Carbon::parse($transaction->date_of_return)->locale('id')->isoFormat('DD MMMM YYYY');
        }
        
        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditampilkan',
            'data'    =>  $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'user_id' => 'required',
            'book_id' => 'required',
        ],
        [
            'user_id.required' => 'This field is required',
            'book_id.required' => 'This field is required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => true,
                'message' => 'Validasi Gagal',
                'error'   => $validator->errors()
            ], 401);
        }

        Book::where('id', $request->book_id)->update([
            'status' => 2
        ]);

        $data = Transaction::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            'loan_date' => \Carbon\Carbon::today()->toDateString(),
            'date_of_return' => \Carbon\Carbon::today()->addDays(10)->toDateString(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditambah',
            'data'    => $data
        ], 200);
    }

    public function show($id)
    {
        try{
            $data = Transaction::with('user', 'book')->findOrfail($id);
            $data->format_loan_date = Carbon::parse($data->loan_date)->locale('id')->isoFormat('DD MMMM YYYY');
            $data->format_date_of_return = Carbon::parse($data->date_of_return)->locale('id')->isoFormat('DD MMMM YYYY');
            $data->makeHidden('loan_date', 'date_of_return');
            return response()->json([
                'status'  => true,
                'message' => 'Data Berhasil Ditampilkan',
                'data'    => $data
            ], 200);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'status'  => true,
                'message' => 'Data Tidak Ditemukan',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),
        [
            'user_id' => 'required',
            'book_id' => 'required',
        ],
        [
            'user_id.required' => 'This field is required',
            'book_id.required' => 'This field is required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'  => true,
                'message' => 'Validasi Gagal',
                'error'   => $validator->errors(),
            ], 401);
        }

        Book::where('id', $request->prevBook_id)->update([
            'status' => 1
        ]);

        Book::where('id', $request->book_id)->update([
            'status' => 2
        ]);

        $data = Transaction::where('id', $id)->update([
            'user_id'  => $request->user_id,
            'book_id'  => $request->book_id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Diupdate',
            'data'    => $data,
        ], 200);
    }

    public function destroy($id)
    {
        try {
            $data = Transaction::findOrfail($id);
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
                'error'   => $e->getMessage(),
            ], 404);
        }
    }

    public function dashboard()
    {
        $users = User::all()->count();
        $books = Book::all()->count();
        $availableBooks     = Book::where('status', 1)->count();
        $notAvailableBooks  = Book::where('status', 2)->count();

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditampilkan',
            'data'    => [
                'users' => $users, 
                'books' => $books, 
                'available'    => $availableBooks, 
                'notAvailable' => $notAvailableBooks]
        ], 200);
    }

    public function myBook($id)
    {
        $data = Transaction::with('book')->where('user_id', $id)->where('return_date', null)->get();

        foreach ($data as $transaction) {
            $transaction->format_loan_date = Carbon::parse($transaction->loan_date)->locale('id')->isoFormat('DD MMMM YYYY');
            $transaction->format_date_of_return = Carbon::parse($transaction->date_of_return)->locale('id')->isoFormat('DD MMMM YYYY');

            $targetDate = Carbon::parse($transaction->date_of_return);
            $today = Carbon::now();
            // $transaction->daysLeft = $targetDate->diffInDays($today);

            if ($targetDate->greaterThan($today)) {
                $transaction->daysLeft = $targetDate->diffInDays($today);
            } else {
                $transaction->daysLeft = 0;
            }

        }
        $data->makeHidden('loan_date');
        $data->makeHidden('date_of_return');

        return response()->json([
            'status'  => true,
            'message' => 'Data Berhasil Ditampilkan',
            'data'    => $data
        ]);
    }

    public function loanBook(Request $request)
    {
        Book::where('id', $request->book_id)->update([
            'status' => 2
        ]);

        $data = Transaction::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            'loan_date' => \Carbon\Carbon::today()->toDateString(),
            'date_of_return' => \Carbon\Carbon::today()->addDays(10)->toDateString(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Buku Berhasil Dipinjam',
            'data'    => $data
        ], 200);
    }

    public function returnMyBook(Request $request, $id)
    {
        Book::where('id', $request->book_id)->update([
            'status' => 1,
        ]);

        $data = Transaction::where('id', $id)->update([
            'return_date' => Carbon::now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Buku Berhasil Dikembalikan',            
            'data'    => $data,
        ],200 );
    }
}
