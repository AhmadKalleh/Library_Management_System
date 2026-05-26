<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'requested_at',
        'expires_at',
        'borrowed_at',
        'due_at',
        'returned_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'expires_at'   => 'datetime',
        'borrowed_at'  => 'datetime',
        'due_at'       => 'datetime',
        'returned_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

}
