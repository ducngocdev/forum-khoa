<?php
namespace App\Services;

use App\Models\{ReputationLog, User};
use Illuminate\Database\Eloquent\Model;

class ReputationService
{
    public static function give(User $user, int $points, string $action, ?Model $source = null): void
    {
        if ($points === 0) return;
        ReputationLog::create([
            'user_id' => $user->id, 'action' => $action, 'points' => $points,
            'source_type' => $source ? $source::class : null, 'source_id' => $source?->id,
        ]);
        $user->update(['reputation' => max(0, $user->reputation + $points)]);
    }
}
