<?php
namespace App\Http\Controllers;

use App\Models\{Answer, Question};
use App\Services\ReputationService;
use Illuminate\Http\Request;

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
        $asker = $r->user();

        if ($prev = $question->acceptedAnswer) {                     // đổi câu trả lời được chấp nhận
            $prev->update(['is_accepted' => false]);
            if ($prev->user_id !== $asker->id) {
                ReputationService::give($prev->user, -15, 'unaccept', $prev);
                ReputationService::give($asker, -2, 'unaccept', $prev);
            }
        }
        if ($prev?->id === $answer->id) { $question->update(['accepted_answer_id' => null]); return back(); }

        $answer->update(['is_accepted' => true]);
        $question->update(['accepted_answer_id' => $answer->id]);
        if ($answer->user_id !== $asker->id) {                       // tự chấp nhận thì không có điểm
            ReputationService::give($answer->user, 15, 'accepted', $answer);
            ReputationService::give($asker, 2, 'accept', $answer);
        }
        return back();
    }

    public function destroy(Request $r, Answer $answer)
    {
        abort_unless($answer->user_id === $r->user()->id, 403);
        $answer->update(['is_hidden' => true]);
        if ($answer->question?->accepted_answer_id === $answer->id) {
            $answer->question->update(['accepted_answer_id' => null]);
        }
        return back()->with('status', 'Đã thu hồi câu trả lời.');
    }
}
