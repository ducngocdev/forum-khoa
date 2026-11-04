<?php
namespace App\Services;

use App\Models\{Answer, Question, Vote, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Bình chọn + chống gian lận:
 *  1. Mỗi người 1 phiếu/bài (unique trong DB); bấm lại cùng chiều = hủy phiếu.
 *  2. Không tự vote bài của mình.
 *  3. Vote xuống cần >= MIN_REP_DOWNVOTE điểm uy tín.
 *  4. Tối đa MAX_PER_HOUR phiếu/giờ.
 *  5. Vote dồn cho cùng 1 tác giả (>= RING_LIMIT phiếu/24h): vẫn ghi nhận nhưng
 *     đánh dấu nghi vấn và KHÔNG tính điểm uy tín.
 */
class VoteService
{
    const MIN_REP_DOWNVOTE = 15;
    const MAX_PER_HOUR = 30;
    const RING_LIMIT = 5;

    public static function cast(User $voter, Model $votable, int $value): void
    {
        $author = $votable->user;
        if ($author->id === $voter->id) throw new \DomainException('Bạn không thể bình chọn bài của chính mình.');
        if ($value < 0 && $voter->reputation < self::MIN_REP_DOWNVOTE)
            throw new \DomainException('Cần ít nhất ' . self::MIN_REP_DOWNVOTE . ' điểm uy tín để bình chọn xuống.');
        if (Vote::where('user_id', $voter->id)->where('updated_at', '>=', now()->subHour())->count() >= self::MAX_PER_HOUR)
            throw new \DomainException('Bạn bình chọn quá nhanh, hãy thử lại sau.');

        DB::transaction(function () use ($voter, $votable, $value, $author) {
            $key = ['user_id' => $voter->id, 'votable_type' => $votable::class, 'votable_id' => $votable->id];
            $vote = Vote::where($key)->first();
            $oldValue = $vote ? $vote->value : 0;
            $oldPts = $vote && !$vote->is_suspicious ? self::points($votable, $vote->value) : 0;

            if ($vote && $vote->value === $value) {
                $vote->delete(); $newValue = 0; $newPts = 0;
            } else {
                $suspicious = self::looksLikeRing($voter, $author);
                $vote = Vote::updateOrCreate($key, ['value' => $value, 'is_suspicious' => $suspicious]);
                $vote->touch();
                $newValue = $value; $newPts = $suspicious ? 0 : self::points($votable, $value);
            }
            $votable->increment('score', $newValue - $oldValue);
            ReputationService::give($author, $newPts - $oldPts, 'vote', $votable);
        });
    }

    private static function points(Model $m, int $value): int
    {
        if ($value < 0) return -2;
        return $m instanceof Answer ? 10 : 5;
    }

    private static function looksLikeRing(User $voter, User $author): bool
    {
        $since = now()->subDay();
        $questionIds = Question::select('id')->where('user_id', $author->id);
        $answerIds = Answer::select('id')->where('user_id', $author->id);

        $n = Vote::where('user_id', $voter->id)
            ->where('updated_at', '>=', $since)
            ->where(function ($query) use ($questionIds, $answerIds) {
                $query->where(function ($query) use ($questionIds) {
                    $query->where('votable_type', Question::class)->whereIn('votable_id', $questionIds);
                })->orWhere(function ($query) use ($answerIds) {
                    $query->where('votable_type', Answer::class)->whereIn('votable_id', $answerIds);
                });
            })->count();

        return $n >= self::RING_LIMIT;
    }
}
