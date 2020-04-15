@extends('layouts.main')

@section('title','Cards Against Schönherz')

@section('content')
    <h1>Turn recap</h1>
    <input type="hidden" id="ready-url" value="{{ route('game.ready', ['game' => $game]) }}">
    <input type="hidden" id="go-to-url" value="{{ route('game.play', ['slug' => $game->slug]) }}">
    <input type="hidden" id="slug" value="{{ $game->slug }}">
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <div class="row">
        <div class="col-md-4">
            <div class="card" style="margin-bottom:20px;">
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($game->players as $player)
                            <li class="list-group-item" id="player-{{ $player->user->id }}">{{ $player->user->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card" id="ready">
                <a href="#" class="btn btn-block btn-primary" id="ready-button">
                    <div class="card-body">
                        Kész
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h4 style="text-align:center;">Nyertes</h4>
                    <div class="card" id="black-card">
                        <div class="card-body" @if($turn->winning_play->player->user->id == Auth::id()) style="background-color:rgba(255,255,255,0.2);" @endif>
                            <h5 class="card-title">
                                @foreach(json_decode($black_card->text) as $key => $black_piece)
                                    {{ $black_piece }} <span class="white-text">@if(isset($turn->winning_play->cards()->get()->toArray()[$key])) {{ implode('',json_decode($turn->winning_play->cards()->get()->toArray()[$key]['text'])) }} @endif</span>
                                @endforeach
                            </h5>
                            <h6 class="card-subtitle" style="text-align:right;">{{ $turn->winning_play->player->user->name }}</h6>
                        </div>
                        <div class="card-footer">
                            <button id="like-button" class="btn btn-default" type="button" onclick="like({{ $turn->winning_play->id }})">
                                <i class="fa fa-thumbs-up"></i>
                            </button>
                            <span id="play-{{ $turn->winning_play->id }}"></span>
                        </div>
                    </div>
                </div>
            </div>
            <h4 style="text-align:center;">Többi beadás</h4>
            <div class="row">
                @foreach($turn->plays as $play)
                    @if($play->id != $turn->winning_play_id)
                        <div class="col-lg-6">
                            <div class="card" id="black-card">
                                <div class="card-body" @if($play->player->user->id == Auth::id()) style="background-color:rgba(255,255,255,0.2);" @endif>
                                    <h5 class="card-title">
                                        @foreach(json_decode($black_card->text) as $key => $black_piece)
                                            {{ $black_piece }} <span class="white-text">@if(isset($play->cards()->get()->toArray()[$key])) {{ implode('',json_decode($play->cards()->get()->toArray()[$key]['text'])) }} @endif</span>
                                        @endforeach
                                    </h5>
                                    <h6 class="card-subtitle" style="text-align:right">{{ $play->player->user->name }}</h6>
                                </div>
                                <div class="card-footer">
                                    <button id="like-button" class="btn btn-default" type="button" onclick="like({{ $play->id }})">
                                        <i class="fa fa-thumbs-up"></i>
                                    </button>
                                    <span id="play-{{ $play->id }}"></span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <input type="hidden" id="like_url" value="{{ route('game.like', ['game' => $game]) }}">
@endsection

@push('scripts')
    <script>
        $('#ready-button').on('click', function(){
            $.ajax({
                url: $('#ready-url').val(),
                data: {
                    _token: $('#_token').val()
                },
                type: "POST",
                dataType: "json",
                success: function(e){
                    console.log(e);
                },
                error:function(e){
                    console.log(e);
                }
            });
            $('#ready').css('display','none');
        });

        let pusher = new Pusher('c294b79228fa69e9f4f5', {
            cluster: 'eu',
            forceTLS: true
        });

        let slug = $('#slug').val();

        let channel = pusher.subscribe('game-' + slug);

        channel.bind('player-ready', function(data) {
            let player = $('#player-' + data.message).html();
            player += ` <i class="fa fa-check"></i>`;
            $('#player-' + data.message).html(player);
        });

        channel.bind('finished-game', function(data){
            window.location = data.message;
        });

        channel.bind('new-turn', function(data){
            window.location = $('#go-to-url').val();
        });

        channel.bind('play-like', function(data) {
            $('#play-' + data.message.id).html(data.message.likes);
        });

        function like(id) {
            $.ajax({
                url: $('#like_url').val(),
                type: "POST",
                dataType: "json",
                data: {
                    _token: $('#_token').val(),
                    play_id: id
                }
            });
            $('#like-button').css('display','none');
        }
        @if($time_left<=0)
            $.ajax({
                url: $('#ready-url').val(),
                data: {
                    _token: $('#_token').val()
                },
                type: "POST",
                dataType: "json",
                success: function(e){
                    console.log(e);
                },
                error:function(e){
                    console.log(e);
                }
            });
            $('#ready').css('display','none');
        @endif
        setTimeout(function(){
            $.ajax({
                url: $('#ready-url').val(),
                data: {
                    _token: $('#_token').val()
                },
                type: "POST",
                dataType: "json",
                success: function(e){
                    console.log(e);
                },
                error:function(e){
                    console.log(e);
                }
            });
            $('#ready').css('display','none');
        }, {{ $time_left }});
    </script>
@endpush
