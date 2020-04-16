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
            <div class="col-md-4">
                <a href="{{ route('games.index') }}" class="btn btn-block btn-lg btn-default">Eddigi játékaim</a>
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
@endsection
