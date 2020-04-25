@extends('layouts.main')

@section('title', 'Új játék')

@section('content')
    <input type="hidden" id="pusher" value="{{ env('PUSHER_APP_KEY') }}">
    <input type="hidden" id="deck_change_url" value="{{ route('game.deck', ['slug' => $game->slug]) }}">
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
                    <div class="form-group">
                        <div class="input-group">
                        <span class="input-group-prepend">
                            <label class="input-group-text" for="rounds">Körök száma</label>
                        </span>
                            <input class="form-control" @if($game->host->id != Auth::id()) readonly @endif style="width:50px; text-align:center;" type="number" id="rounds" placeholder="Körök" value="{{ $game->number_of_rounds ? $game->number_of_rounds : 3 }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                        <span class="input-group-prepend">
                            <label class="input-group-text" for="deck">Pakli</label>
                        </span>
                            <select class="form-control" @if($game->host->id != Auth::id()) disabled @endif name="deck" id="deck">
                                <option selected disabled>Válassz egyet!</option>
                                @foreach($decks as $deck)
                                    <option @if($game->deck_id == $deck->id) selected @endif id="deck-{{ $deck->id }}" value="{{ $deck->id }}">{{ $deck->name }} - @if($deck->user->nickname){{ $deck->user->nickname }}@else {{ $deck->user->name }} @endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-group" id="start-button">
                        @if($game->host->id == Auth::id())
                            <button style="margin-top:10px; @if(!$everyone_ready) display:none; @endif" type="button" class="btn btn-block btn-primary" id="start">Indítás</button>
                        @else
                            @if(Auth::user()->player() == null || !Auth::user()->player()->ready)
                                <button style="margin-top:10px" type="button" class="btn btn-block btn-primary" id="ready">Ready</button>
                            @else
                                <button style="margin-top:10px" type="button" class="btn btn-block btn-default" id="ready">Unready</button>
                            @endif
                        @endif
                    </div>
                    <div class="input-group">
                        <div id="deck-alert" class="alert alert-warning show hide alert-dismissible" style="width:100%; display:none;">
                            <p style="margin-bottom:0;" class="text-dark"><i class="fa fa-exclamation-triangle"></i> Nem választottál paklit!</p>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
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
                            <li @if($player->user->id == Auth::id()) data-toggle="tooltip" title="Ez vagy Te!" style="font-weight:bold; background:rgba(255,255,255,0.2);" @endif class="list-group-item" id="player_{{ $player->id }}">@if($player->user->nickname) {{ $player->user->nickname }} @else {{ $player->user->name }} @endif @if($player->user->id == $game->host->id) <i data-toggle="tooltip" title="Játékvezető" class="fa fa-crown"></i>@endif
                                @if($player->user->id != $game->host->id)
                                    @if($player->ready)
                                        &nbsp;<i id="ready-{{ $player->id }}" class="fa fa-check" style="display:inline"></i>
                                    @else
                                        &nbsp;<i id="ready-{{ $player->id }}" class="fa fa-check" style="display:none"></i>
                                    @endif
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="slug" value="{{ $game->slug }}">
    <input type="hidden" id="user_id" value="{{ Auth::id() }}">
    <input type="hidden" id="player_id" value="{{ $player_id }}">
    <input type="hidden" id="change_url" value="{{ route('game.change', ['slug' => $game->slug]) }}">
    <input type="hidden" id="start_game_url" value="{{ route('game.start', ['game' => $game]) }}">
    <input type="hidden" id="game_url" value="{{ route('game.play', ['slug' => $game->slug]) }}">
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <input type="hidden" id="ready_url" value="{{ route('game.lobby.ready', ['slug' => $game->slug]) }}">
@endsection

@push('modals')
    <div class="modal fade" id="waiting-for-game">
        <div class="modal-dialog" style="padding:0; margin:0;">
            <div class="modal-content" style="background:rgba(0,0,0,0.5); width:100vw; height:100vh;">
                <h2 style="text-align:center; margin-top:45vh; margin-bottom:0">Várakozás <i class="fa fa-spin fa-wheelchair"></i></h2>
                <h3 style="text-align:center;  margin-top:0; padding:5px;" id="wait-text"></h3>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        function getRandomInt(max) {
            return Math.floor(Math.random() * Math.floor(max));
        }
        let messages = [
            @foreach($messages as $message)
            '{{ $message }}',
            @endforeach
        ];
        setInterval(function(){
            $('#wait-text').html(messages[getRandomInt(messages.length)]);
        }, 3000);
        setInterval(function(){
            let text = $('#wait-text').html();
            text += `.`;
            $('#wait-text').html(text);
        },1000);
    </script>
    <script src="{{ asset('js/game.js') }}"></script>
@endpush
