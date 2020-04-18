@extends('layouts.admin')

@section('admin.games.active', 'active')
@section('title','Játékosok')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Játékosok &raquo; <a href="{{ route('admin.games.view', ['game' => $game]) }}">Vissza</a></h4>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        @foreach($game->getPlayers() as $player)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.players.view', ['player' => $player]) }}">{{ $player->user->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
