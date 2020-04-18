<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\BugReport;
use Auth;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $plays = Play::withTrashed()->where('featured',true)->get()->shuffle(random_int(0,1000));

        if($plays->count() >= 9){
            $plays = $plays->random(9)->shuffle();
        }

        return view('index',[
            'featured_plays' => $plays
        ]);
    }

    public function disclaimer()
    {
        return view('disclaimer');
    }

    public function report()
    {
        return view('report');
    }

    public function submitReport(Request $request)
    {
        if(Auth::user()->bugReports()->where('resolved',0)->count()>5) {
            abort(403);
        }

        $page = $request->input('page');
        $description = $request->input('description');
        $trace = $request->input('trace');

        $report = new BugReport();
        $report->page = $page;
        $report->description = $description;
        $report->trace = $trace;
        $report->user_id = Auth::id();
        $report->save();

        return redirect()->route('index');
    }
}
