@extends('layouts.main')

@section('title', 'Game')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <a href="{{ route('leave') }}" class="btn btn-default btn-block">
                    <div class="card-body">
                        Kilépek
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card" style="margin-bottom:20px;">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-prepend">
                            <label for="game-slug" class="input-group-text">Meghívó</label>
                        </span>
                        <input data-toggle="tooltip" title="Katt a másoláshoz!" readonly type="text" class="form-control" id="game-slug" value="{{ route('join', ['slug' => $game->slug]) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-body">
                    <h5 class="card-title">Beállítások</h5>
                    <div class="input-group">
                        <span class="input-group-prepend">
                            <label class="input-group-text" for="rounds">Körök száma</label>
                        </span>
                        <input class="form-control" @if($game->host->id != Auth::id()) readonly @endif style="width:50px; text-align:center;" type="number" id="rounds" placeholder="Körök" value="{{ $game->number_of_rounds ? $game->number_of_rounds : 3 }}">
                    </div>
                    <div class="input-group" id="start-button">
                        @if($game->host->id == Auth::id())
                            <button style="margin-top:10px;" type="button" class="btn btn-block btn-primary" id="start">Indítás</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Résztvevők</h5>
                    <ul class="list-group" id="user-list">
                        @foreach($game->players as $player)
                            <li @if($player->user->id == Auth::id()) data-toggle="tooltip" title="Ez vagy Te!" style="font-weight:bold; background:rgba(255,255,255,0.2);" @endif class="list-group-item" id="player_{{ $player->id }}">{{ $player->user->name }}@if($player->user->id == $game->host->id) <i data-toggle="tooltip" title="Játékvezető" class="fa fa-crown"></i>@endif</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="slug" value="{{ $game->slug }}">
    <input type="hidden" id="user_id" value="{{ Auth::id() }}">
    <input type="hidden" id="player_id" value="{{ $player_id }}">
    <input type="hidden" id="change_url" value="{{ route('game.change', ['game' => $game]) }}">
    <input type="hidden" id="start_game_url" value="{{ route('game.start', ['game' => $game]) }}">
    <input type="hidden" id="game_url" value="{{ route('game.play', ['slug' => $game->slug]) }}">
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@push('scripts')
    <script src="{{ asset('js/game.js') }}"></script>
@endpush
