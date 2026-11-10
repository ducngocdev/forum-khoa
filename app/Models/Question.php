<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [];
    protected $casts = ['is_hidden' => 'boolean', 'accepted_answer_id' => 'integer'];
    public function user() { return $this->belongsTo(User::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function answers() { return $this->hasMany(Answer::class); }
    public function media() { return $this->hasMany(QuestionMedia::class); }
    public function acceptedAnswer() { return $this->belongsTo(Answer::class, 'accepted_answer_id'); }
    public function tags() { return $this->belongsToMany(Tag::class); }
    public function comments() { return $this->morphMany(Comment::class, 'commentable'); }
    public function votes() { return $this->morphMany(Vote::class, 'votable'); }
    public function reports() { return $this->morphMany(Report::class, 'reportable'); }
}
