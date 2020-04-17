@extends('layouts.main')

@section('title','Eddigi játékaim')

@section('content')
    <h2>Eddigi játékaim &raquo; <a href="{{ route('index') }}">Főoldal</a></h2>
    <div class="row">
        @foreach($games as $game)
            <div class="col-md-12" style="margin-bottom:15px;">
                <a href="{{ route('game.finished', ['slug' => $game->slug]) }}" class="card-link">
                    <div class="card" style="border:1px solid rgba(255,255,255,0.4);">
                        <div class="card-header">
                            <i>{{ $game->created_at }}</i>
                        </div>
                        <div class="card-body" style="background-color:rgba(255,255,255,0.05) !important;">
                            {{ $game->players()->onlyTrashed()->get()->count() }} játékos
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
