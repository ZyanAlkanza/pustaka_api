<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transactions')->insert([
            [
                'user_id'   => 2,
                'book_id'   => 2,
                'loan_date' => \Carbon\Carbon::today()->toDateString(),
                'date_of_return' => \Carbon\Carbon::today()->addDays(10)->toDateString(),
            ],
            [
                'user_id'   => 3,
                'book_id'   => 3,
                'loan_date' => \Carbon\Carbon::today()->toDateString(),
                'date_of_return' => \Carbon\Carbon::today()->addDays(10)->toDateString(),
            ]
            
        ]);
    }
}
