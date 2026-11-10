<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            $paths = QuestionMedia::query()
                ->whereIn('question_id', $user->questions()->select('id'))
                ->pluck('path')
                ->all();
            $user->setAttribute('question_media_paths', $paths);
        });
        static::deleted(fn (User $user) => Storage::disk('public')->delete($user->getAttribute('question_media_paths') ?? []));
    }

    protected $fillable = [
        'name', 'email', 'password', 'role', 'reputation', 'is_banned',
        'avatar_path', 'academic_title', 'bio', 'class_name', 'study_year', 'academic_year',
    ];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_banned' => 'boolean'];
    }
    public function getDisplayReputationAttribute(): int { return max(0, (int) $this->reputation); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isModerator(): bool { return in_array($this->role, ['moderator', 'admin']); }
    public function questions() { return $this->hasMany(Question::class); }
    public function answers() { return $this->hasMany(Answer::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function votes() { return $this->hasMany(Vote::class); }
    public function reports() { return $this->hasMany(Report::class, 'reporter_id'); }
    public function handledReports() { return $this->hasMany(Report::class, 'handled_by'); }
    public function reputationLogs() { return $this->hasMany(ReputationLog::class); }
    public function books() { return $this->hasMany(Book::class); }
}
