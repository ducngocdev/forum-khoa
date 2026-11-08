<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'user_id', 'title', 'author', 'description', 'condition',
        'price', 'type', 'status', 'contact', 'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
