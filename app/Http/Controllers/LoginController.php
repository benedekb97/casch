<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Str;

class LoginController extends Controller
{
    public function redirect(Request $request)
    {
        $auth_sch_id = env('AUTH_SCH_ID');
        $auth_sch_key = env('AUTH_SCH_KEY');
        $ip = md5($request->ip());
        $redirect_uri = env('APP_URL') . "/callback";

        $scope = [
            'basic',
            'displayName',
            'sn',
            'givenName',
            'mail',
            'eduPersonEntitlement'
        ];

        $new_scope = "";

        foreach($scope as $scope_part) {
            $new_scope .= $scope_part . "+";
        }

        $url = "https://auth.sch.bme.hu/site/login?client_id=" . $auth_sch_id . "&redirect_uri=" . $redirect_uri
            . "&scope=" . $new_scope . "&response_type=code&state=" . $ip;

        return redirect($url);
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');

        $auth_sch_id = env('AUTH_SCH_ID');
        $auth_sch_key = env('AUTH_SCH_KEY');

        $url = "https://auth.sch.bme.hu/oauth2/token";
        $post_fields = "grant_type=authorization_code&code=$code";

        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_USERPWD,"$auth_sch_id:$auth_sch_key");
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$post_fields);
        $result = curl_exec($curl);

        $result = json_decode($result);

        if(isset($result->access_token)) {
            $access_token = $result->access_token;
        }else{
            abort(400);
        }

        $url = "https://auth.sch.bme.hu/api/profile?access_token=$access_token";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result);

        $user = User::all()->where('email', $result->mail)->first();

        if($user == null) {
            $user = new User();
            $user->email = $result->mail;
            $user->internal_id = $result->internal_id;
            $user->name = $result->displayName;
            $user->save();

            $user->assignGroup('default');

            Auth::login($user);
        }else{
            Auth::login($user);
        }

        if(Session::get('game_slug')!="" || $user->player()!=null) {
            $slug = Session::get('game_slug') ? Session::get('game_slug') : $user->game()->slug;
            if(Session::has('game_slug')){
                Session::forget('game_slug');
            }
            return redirect()->route('join', $slug);
        }

        return redirect()->route('index');
    }

    public function logout()
    {
        if(Auth::check()) {
            Auth::logout();
        }

        return redirect()->route('index');
    }

    public function test()
    {
        if(Auth::check()) {
            Auth::logout();
        }

        Auth::login(User::all()->where('email','test')->first());

        if(Auth::user()->game() != null) {
            if(Auth::user()->game()->host_user_id == Auth::id()) {
                return redirect()->route('host');
            }else{
                return redirect()->route('join', ['slug' => Auth::user()->game()->slug]);
            }
        }

        return redirect()->back();
    }

    public function login(Request $request)
    {
        if(Auth::check()) {
            redirect()->route('index');
        }

        if($request->input('email') && $request->input('password')) {
            $email = $request->input('email');
            $user = User::all()->where('email',$email)->first();

            $credentials = [
                'email' => $email,
                'password' => $request->input('password')
            ];

            if(Auth::attempt($credentials)) {
                if(Session::has('game_slug') || $user->game()!=null) {
                    $slug = Session::get('game_slug') ? Session::get('game_slug') : $user->game()->slug;
                    if(Session::has('game_slug')){
                        Session::forget('game_slug');
                    }
                    return redirect()->route('join', $slug);
                }

                return redirect()->route('index');
            }

            $error = 'Hibás email cím vagy jelszó!';

            if($user && !$user->password) {
                $error = 'Jelentkezz be AuthSCH-val!';
            }
        }else{
            $email = null;
            $error = null;
        }


        return view('login', [
            'email' => $email,
            'error' => $error
        ]);
    }

    public function register(Request $request)
    {
        if($request->input('email') && $request->input('password1') && $request->input('password2') && $request->input('name')){

            if(User::all()->where('email',$request->input('email'))->count() > 0){
                $error = 'Ilyen email címmel már regisztráltak az oldalon!';
            }elseif($request->input('password1') !== $request->input('password2')){
                $error = 'Nem egyezett meg a két megadott jelszó!';
            }elseif(strlen($request->input('password1'))<8){
                $error = 'Minimum 8 karakter hosszú legyen a jelszavad!';
            }else{
                $user = new User();
                $user->email = $request->input('email');
                $user->password = bcrypt($request->input('password1'));
                $user->name = $request->input('name');
                $user->nickname = $request->input('nickname');
                $user->internal_id  = Str::random(30);
                $user->save();

                $user->assignGroup('default');

                Mail::send('email.register', ['user' => $user], function($m) use ($user){
                    $m->from('cards.against.sch@gmail.com', 'Cards Against Schönherz');

                    $m->to($user->email, $user->name)->subject('Aktiváld felhasználód!');
                });

                Auth::login($user);

                return redirect()->route('activate');
            }

            $email = $request->input('email');
            $name = $request->input('name');
            $nickname = $request->input('nickname');

        }else{
            $email = null;
            $name = null;
            $error = null;
            $nickname = null;
        }


        return view('register',[
            'email' => $email,
            'name' => $name,
            'error' => $error,
            'nickname' => $nickname
        ]);
    }

    public function activate($code = null)
    {

        return view('activate');
    }

    public function activateCode($code)
    {
        if($code) {
            $user = User::where('internal_id', $code)->first();

            if ($user) {
                $user->activated = true;
                $user->save();
            }
        }

        return view('activated');

    }

    public function resend()
    {
        $user = Auth::user();
        if(!$user)
        {
            return redirect()->route('login');
        }

        $last_email = $user->last_email_sent;


        if(!$last_email || (time()-strtotime($last_email))>24*60*60) {
            Mail::send('email.register', ['user' => $user], function($m) use ($user){
                $m->from('cards.against.sch@gmail.com', 'Cards Against Schönherz');

                $m->to($user->email, $user->name)->subject('Aktiváld felhasználód!');
            });

            $user->last_email_sent = date("Y-m-d H:i:s");
            $user->save();
        }

        return redirect()->route('activate');
    }

}
