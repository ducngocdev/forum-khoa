<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $totalQuestions = Question::where('is_hidden', false)->count();
        $answeredQuestions = Question::where('is_hidden', false)->whereNotNull('accepted_answer_id')->count();
        $totalMembers = DB::table('users')->where('is_banned', false)->count();

        $subjects = DB::table('v_subject_stats')->orderByDesc('total_questions')->get();

        $latestQuestions = Question::with(['user', 'subject', 'tags'])
            ->withCount('answers')
            ->where('is_hidden', false)
            ->latest()
            ->limit(6)
            ->get();

        $topContributors = DB::table('v_top_contributors')
            ->leftJoin('users as contributor', 'contributor.id', '=', 'v_top_contributors.id')
            ->select('v_top_contributors.*', 'contributor.avatar_path')
            ->where('contributor.is_banned', false)
            ->orderByDesc('v_top_contributors.reputation')
            ->limit(5)
            ->get();
        $popularTags = Tag::withCount('questions')->orderByDesc('questions_count')->limit(12)->get();

        return view('home', compact(
            'totalQuestions',
            'answeredQuestions',
            'totalMembers',
            'subjects',
            'latestQuestions',
            'topContributors',
            'popularTags'
        ));
    }
}
