<?php
namespace App\Http\Controllers;

use App\Models\{Report, Vote};
use App\Services\{AcceptedAnswerService, ReputationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{
    private function guard(Request $r) { abort_unless($r->user()->isModerator(), 403); }

    public function index(Request $r)
    {
        $this->guard($r);
        $reports = Report::with(['reporter', 'reportable.user'])->where('status', 'pending')->latest()->get();
        $suspicious = Vote::with(['user', 'votable.user'])->where('is_suspicious', true)->latest()->limit(20)->get();
        return view('moderation.index', compact('reports', 'suspicious'));
    }

    public function hide(Request $r, Report $report)
    {
        $this->guard($r);
        DB::transaction(function () use ($r, $report) {
            if ($t = $report->reportable) {
                if ($t instanceof \App\Models\Answer) {
                    AcceptedAnswerService::clearForAnswer($t);
                }
                $t->update(['is_hidden' => true]);
                ReputationService::give($t->user, -20, 'content_removed', $t);
            }
            Report::where('reportable_type', $report->reportable_type)->where('reportable_id', $report->reportable_id)
                ->where('status', 'pending')->update(['status' => 'resolved', 'handled_by' => $r->user()->id]);
        });
        return back();
    }

    public function dismiss(Request $r, Report $report)
    {
        $this->guard($r);
        $report->update(['status' => 'rejected', 'handled_by' => $r->user()->id]);
        return back();
    }
}
