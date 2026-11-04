<?php
namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show(User $user)
    {
        $logs = $user->reputationLogs()->latest()->limit(20)->get();
        $questions = $user->questions()->where('is_hidden', false)->latest()->limit(5)->get();
        $answersCount = $user->answers()->where('is_hidden', false)->count();
        $acceptedAnswersCount = $user->answers()->where('is_hidden', false)->where('is_accepted', true)->count();

        return view('users.show', compact('user', 'logs', 'questions', 'answersCount', 'acceptedAnswersCount'));
    }
}
