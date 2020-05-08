<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Play;
use App\Models\BugReport;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Junges\ACL\Http\Models\Group;

class SiteController extends Controller
{
    public function index()
    {
        $plays = Play::withTrashed()->where('featured',true)->get()->shuffle(random_int(0,1000));

        if($plays->count() >= 9){
            $plays = $plays->random(9)->shuffle();
        }

        if(Auth::check()) {
            $butthurt = Auth::user()->message_seen === 0;

            Auth::user()->message_seen = 1;
            Auth::user()->save();
        }else{
            $butthurt = false;
        }

        return view('index',[
            'featured_plays' => $plays,
            'butthurt' => $butthurt
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

        $admins = Group::where('slug','admin')->first()->users;

        foreach($admins as $admin) {
            $email = new Email();
            $email->to_email = $admin->email;
            $email->to_name = $admin->name;
            $email->from_email = 'cards.against.sch@gmail.com';
            $email->from_name = 'Cards Against Schönherz';
            $email->subject = 'Új hibajelentés - #' . $report->id;
            $email->body = view('email.bug_report', ['report' => $report, 'user' => $admin])->render();
            $email->save();

            $email->send();
        }

        return redirect()->route('index');
    }

    public function help()
    {
        return view('help');
    }
}
