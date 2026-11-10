<?php
namespace App\Services;

use App\Models\{ReputationLog, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReputationService
{
    public static function give(User $user, int $points, string $action, ?Model $source = null): void
    {
        if ($points === 0) return;

        DB::transaction(function () use ($user, $points, $action, $source) {
            self::record($user, $action, $points, $source);
            $user->increment('reputation', $points);
        });
    }

    public static function record(User $user, string $action, int $points = 0, ?Model $source = null): void
    {
        ReputationLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'points' => $points,
            'source_type' => $source ? $source::class : null,
            'source_id' => $source?->id,
        ]);
    }
}
