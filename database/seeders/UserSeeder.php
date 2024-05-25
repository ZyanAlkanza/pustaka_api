<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'Zyan Mujadid Alkanza',
                'email' => 'zyan.mujaddid@gmail.com',
                'address' => 'Puri Kencana Blok D7 Pengasinan-Rawalumbu',
                'join_date' => \Carbon\Carbon::today()->toDateString(),
                'gender' => 'L',
                'phone' => '089653534708',
                'role' => '1',
                'password' => Hash::make('123'),
            ],
            [
                'username' => 'Azhari Nur Fauzi',
                'email' => 'azhari.fauzi@gmail.com',
                'address' => 'Pondok Timur Indah Blok H No 53 Jatimulya-Tambun Selatan ',
                'join_date' => \Carbon\Carbon::today()->toDateString(),
                'gender' => 'L',
                'phone' => '081313700700',
                'role' => '3',
                'password' => Hash::make('123'),
            ],
        ]);
    }
}
