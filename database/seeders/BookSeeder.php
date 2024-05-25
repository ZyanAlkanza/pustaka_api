<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books')->insert([
            [
                'title'  => 'Sebuah Seni Untuk Bersikap Bodo Amat',
                'author' => 'Mark Manson',
                'status' => 1,
                'book_detail' => 'Sebuah buku berjudul Sebuah Seni Untuk Bersikap Bodo Amat karya Mark Manson',
                'image'  => 'cover.png'
            ],
            [
                'title'  => 'Kamu (Tidak) Istimewa',
                'author' => 'Natasha Rizky',
                'status' => 1,
                'book_detail' => 'Sebuah buku berjudul Kamu (Tidak) Istimewa karya Natasha Rizky',
                'image'  => 'cover.png'
            ],
            [
                'title'  => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'status' => 1,
                'book_detail' => 'Sebuah buku berjudul Laskar Pelangi karya Andrea Hirata',
                'image'  => 'cover.png'
            ],
        ]);
    }
}
