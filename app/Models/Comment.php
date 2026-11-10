<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];
    protected $casts = ['is_hidden' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function commentable() { return $this->morphTo(); }
    public function votes() { return $this->morphMany(Vote::class, 'votable'); }
    public function reports() { return $this->morphMany(Report::class, 'reportable'); }
}
