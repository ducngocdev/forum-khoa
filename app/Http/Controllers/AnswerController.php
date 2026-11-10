<?php
namespace App\Http\Controllers;

use App\Models\{Answer, Question};
use App\Services\AcceptedAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    public function store(Request $r, Question $question)
    {
        abort_if($question->is_hidden, 404);
        $d = $r->validate(['body' => 'required|string|min:10']);
        $question->answers()->create(['user_id' => $r->user()->id, 'body' => $d['body']]);
        return back();
    }

    public function accept(Request $r, Answer $answer)
    {
        $question = $answer->question;
        abort_unless($question->user_id === $r->user()->id, 403);
        abort_if($question->is_hidden || $answer->is_hidden, 404);

        AcceptedAnswerService::toggle($question, $answer);
        return back();
    }

    public function destroy(Request $r, Answer $answer)
    {
        abort_unless($answer->user_id === $r->user()->id, 403);
        DB::transaction(function () use ($answer) {
            AcceptedAnswerService::clearForAnswer($answer);
            $answer->update(['is_hidden' => true]);
        });
        return back()->with('status', 'Đã thu hồi câu trả lời.');
    }
}
