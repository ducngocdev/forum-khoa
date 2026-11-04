<?php
namespace App\Http\Controllers;

use App\Models\Report;
use App\Support\Forum;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $r)
    {
        $d = $r->validate(['type' => 'required|in:question,answer,comment', 'id' => 'required|integer', 'reason' => 'required|string|min:5|max:255']);
        $target = Forum::resolve($d['type'], $d['id']);
        Report::firstOrCreate(
            ['reporter_id' => $r->user()->id, 'reportable_type' => $target::class, 'reportable_id' => $target->id],
            ['reason' => $d['reason']]
        );
        return back()->with('status', 'Đã gửi báo cáo, kiểm duyệt viên sẽ xem xét.');
    }
}
