@extends('layouts.main')

@section('title','Cards against Schönherz')

@section('content')
    <div class="row">
        @auth
            <div class="col-md-4">
                <a href="{{ route('logout') }}" class="btn btn-block btn-lg btn-default">Kijelentkezés</a>
            </div>
            @if(Auth::user()->player() == null)
                @can('host')
                    <div class="col-md-4">
                        <a href="{{ route('host') }}" class="btn btn-block btn-lg btn-default">Új játék</a>
                    </div>
                @endcan
            @else
                <div class="col-md-4">
                    <a href="{{ route('join', ['slug' => Auth::user()->game()->slug]) }}" class="btn btn-block btn-lg btn-default">Visszacsatlakozás</a>
                </div>
            @endif
            @can('edit-cards')
                <div class="col-md-4">
                    <a href="{{ route('cards.index') }}" class="btn btn-block btn-lg btn-default">Kártyák</a>
                </div>
            @endcan
            @group('admin')
                <div class="col-md-4">
                    <a href="{{ route('admin.index') }}" class="btn btn-block btn-lg btn-default">Admin</a>
                </div>
            @endgroup
            <div class="col-md-4">
                <a href="{{ route('games.index') }}" class="btn btn-block btn-lg btn-default">Eddigi játékaim</a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('user.profile') }}" class="btn btn-block btn-lg btn-default">Profilom</a>
            </div>
        @else
            <div class="col-md-4">
                <a href="{{ route('login') }}" class="btn btn-block btn-lg btn-default">Bejelentkezés</a>
            </div>
            @if($_SERVER['REMOTE_ADDR']=='192.168.0.1')
                <div class="col-md-4">
                    <a href="{{ route('test') }}" class="btn btn-block btn-lg btn-default">TEST</a>
                </div>
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="file" name="file">
                    <input type="submit">
                </form>
            @endif
        @endauth
    </div>
    @auth
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h4 class="card-title"><b>Kiemelkedő minőségű kártyák</b></h4>
                </div>
            </div>
        </div>
        @foreach($featured_plays as $play)
            <div class="col-md-4">
                @if(in_array(Auth::id(),$play->getUsers()))
                    <a class="card-link-black" href="{{ route('game.finished', ['slug' => $play->getGame()->slug]) }}">
                @endif
                    <div class="card" id="black-card">
                        <div class="card-body">
                            <h5 class="card-title">{!! $play->getTextHTML() !!}</h5>
                            <p style="text-align:right; font-size:10pt;">{{ $play->getPlayer()->user->nickname ? $play->getPlayer()->user->nickname : $play->getPlayer()->user->name }}</p>
                        </div>
                    </div>
                @if(in_array(Auth::id(),$play->getUsers()))
                    </a>
                @endif
            </div>
        @endforeach
    </div>
    @endauth
@endsection
