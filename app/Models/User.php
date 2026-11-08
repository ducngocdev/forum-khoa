<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name', 'email', 'password', 'role', 'reputation', 'is_banned',
        'avatar_path', 'academic_title', 'bio', 'class_name', 'study_year', 'academic_year',
    ];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_banned' => 'boolean'];
    }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isModerator(): bool { return in_array($this->role, ['moderator', 'admin']); }
    public function questions() { return $this->hasMany(Question::class); }
    public function answers() { return $this->hasMany(Answer::class); }
    public function reputationLogs() { return $this->hasMany(ReputationLog::class); }
    public function books() { return $this->hasMany(Book::class); }
}
