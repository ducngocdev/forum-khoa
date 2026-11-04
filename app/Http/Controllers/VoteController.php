<?php
namespace App\Http\Controllers;

use App\Services\VoteService;
use App\Support\Forum;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function store(Request $r)
    {
        $d = $r->validate(['type' => 'required|in:question,answer', 'id' => 'required|integer', 'value' => 'required|in:1,-1']);
        try {
            VoteService::cast($r->user(), Forum::resolve($d['type'], $d['id']), (int) $d['value']);
        } catch (\DomainException $e) {
            return back()->withErrors(['vote' => $e->getMessage()]);
        }
        return back();
    }
}
