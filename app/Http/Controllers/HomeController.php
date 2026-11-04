<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $totalQuestions = Question::where('is_hidden', false)->count();
        $answeredQuestions = Question::where('is_hidden', false)->whereNotNull('accepted_answer_id')->count();
        $totalMembers = DB::table('users')->count();

        $subjects = DB::table('v_subject_stats')->orderByDesc('total_questions')->get();

        $latestQuestions = Question::with(['user', 'subject', 'tags'])
            ->withCount('answers')
            ->where('is_hidden', false)
            ->latest()
            ->limit(6)
            ->get();

        $topContributors = DB::table('v_top_contributors')
            ->orderByDesc('reputation')
            ->limit(5)
            ->get();

        return view('home', compact(
            'totalQuestions',
            'answeredQuestions',
            'totalMembers',
            'subjects',
            'latestQuestions',
            'topContributors'
        ));
    }
}

