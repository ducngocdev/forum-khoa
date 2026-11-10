<?php

namespace App\Services;

use App\Models\{Answer, Question, User};
use Illuminate\Support\Facades\DB;

class AcceptedAnswerService
{
    public static function toggle(Question $question, Answer $answer): void
    {
        DB::transaction(function () use ($question, $answer) {
            $question = Question::whereKey($question->id)->lockForUpdate()->firstOrFail();
            $previous = $question->acceptedAnswer()->first();
            $asker = $question->user;

            if ($previous?->is($answer)) {
                $question->update(['accepted_answer_id' => null]);
                self::revoke($previous, $asker);
                return;
            }

            if ($previous) {
                self::revoke($previous, $asker);
            }

            $question->update(['accepted_answer_id' => $answer->id]);
            if ($answer->user_id !== $asker->id) {
                ReputationService::give($answer->user, 15, 'accepted', $answer);
                ReputationService::give($asker, 2, 'accept', $answer);
            }
        });
    }

    public static function clearForAnswer(Answer $answer): void
    {
        DB::transaction(function () use ($answer) {
            $question = Question::whereKey($answer->question_id)->lockForUpdate()->first();
            if (! $question || $question->accepted_answer_id !== $answer->id) {
                return;
            }

            $question->update(['accepted_answer_id' => null]);
            self::revoke($answer, $question->user);
        });
    }

    public static function clearForUser(User $user): void
    {
        Answer::where('user_id', $user->id)
            ->whereIn('id', Question::select('accepted_answer_id')->whereNotNull('accepted_answer_id'))
            ->with('question.user')
            ->get()
            ->each(fn (Answer $answer) => self::clearForAnswer($answer));
    }

    private static function revoke(Answer $answer, User $asker): void
    {
        if ($answer->user_id === $asker->id) {
            return;
        }

        ReputationService::give($answer->user, -15, 'unaccept', $answer);
        ReputationService::give($asker, -2, 'unaccept', $answer);
    }
}
