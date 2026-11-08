<?php
namespace App\Http\Controllers;

use App\Support\Forum;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $r)
    {
        $d = $r->validate(['type' => 'required|in:question,answer', 'id' => 'required|integer', 'body' => 'required|string|min:3|max:1000']);
        Forum::resolve($d['type'], $d['id'])->comments()->create(['user_id' => $r->user()->id, 'body' => $d['body']]);
        return back();
    }

    public function destroy(Request $r, \App\Models\Comment $comment)
    {
        abort_unless($comment->user_id === $r->user()->id, 403);
        $comment->update(['is_hidden' => true]);
        return back()->with('status', 'Đã thu hồi bình luận.');
    }
}
