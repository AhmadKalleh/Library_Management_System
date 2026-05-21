<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{

    protected $fillable = [
        'title',
        'author',
        'category_id',
        'available_copies',
        'borrowed_copies',
        'status',
    ];

    protected $casts = [
        'available_copies' => 'integer',
        'borrowed_copies'  => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
