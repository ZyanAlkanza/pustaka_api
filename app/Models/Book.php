<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'status',
        'book_detail',
        'image'
    ];

    public function transaction(){
        return $this->hasMany(Transaction::class);
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
