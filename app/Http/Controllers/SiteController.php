<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Email;
use App\Models\Game;
use App\Models\Play;
use App\Models\BugReport;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Junges\ACL\Http\Models\Group;

class SiteController extends Controller
{
    public function index()
    {
        $plays = Play::withTrashed()->where('featured',true)->get()->shuffle(random_int(0,1000));

        if($plays->count() > 6){
            $plays = $plays->random(6)->shuffle();
        }

        $games_count = Game::withTrashed()->get()->count();
        $players_count = User::withTrashed()->get()->count();
        $plays_count = Play::withTrashed()->get()->count();
        $games_today = Game::withTrashed()->where('created_at','>',date('Y-m-d H:i:s', time()-24*60*60))->where('started',1)->get()->count();

        $most_played = DB::query()->selectRaw('count(id), card_id')->from('play_card')->groupBy('card_id')->orderByRaw('count(card_id) desc')->get()->first();

        $asd = 'count(id)';

        $most_played_id = $most_played->card_id;
        $most_played = $most_played->$asd;

        $most_played_card = Card::withTrashed()->where('id',$most_played_id)->first();

        if(Auth::check()) {
            $butthurt = Auth::user()->message_seen === 0;

            Auth::user()->message_seen = 1;
            Auth::user()->save();
        }else{
            $butthurt = false;
        }

        return view('index',[
            'featured_plays' => $plays,
            'butthurt' => $butthurt,
            'games_count' => $games_count,
            'players_count' => $players_count,
            'plays_count' => $plays_count,
            'games_today' => $games_today,
            'most_played_card' => $most_played_card,
            'most_played' => $most_played
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
