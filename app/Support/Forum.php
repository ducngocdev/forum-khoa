<?php
namespace App\Support;

use App\Models\{Answer, Comment, Question};

class Forum
{
    const TYPES = ['question' => Question::class, 'answer' => Answer::class, 'comment' => Comment::class];

    public static function resolve(string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        return self::TYPES[$type]::findOrFail($id);
    }
}
