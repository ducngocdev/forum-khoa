<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $guarded = [];
    protected $casts = ['is_accepted' => 'boolean', 'is_hidden' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function question() { return $this->belongsTo(Question::class); }
    public function comments() { return $this->morphMany(Comment::class, 'commentable'); }
    public function votes() { return $this->morphMany(Vote::class, 'votable'); }
}
