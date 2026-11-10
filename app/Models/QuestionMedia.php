<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuestionMedia extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleted(fn (QuestionMedia $media) => Storage::disk('public')->delete($media->path));
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getIsVideoAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }
}
