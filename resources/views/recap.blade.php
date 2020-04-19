@extends('layouts.main')

@section('title','Cards Against Schönherz')

@section('content')
    <audio id="pop">
        <source src="{{ asset('audio/pop.mp3') }}" type="audio/mpeg">
    </audio>
    <h1>Turn recap</h1>
    <input type="hidden" id="ready-url" value="{{ route('game.ready', ['game' => $game]) }}">
    <input type="hidden" id="go-to-url" value="{{ route('game.play', ['slug' => $game->slug]) }}">
    <input type="hidden" id="slug" value="{{ $game->slug }}">
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <input type="hidden" id="pusher" value="{{ env('PUSHER_APP_KEY') }}">
    <div class="row">
        <div class="col-lg-4">
            <div class="card" style="margin-bottom:20px;">
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($game->players as $player)
                            <li style="border-bottom:none; margin-bottom:0; padding-bottom:0;" class="list-group-item" id="player-{{ $player->user->id }}">@if($player->user->nickname) {{ $player->user->nickname }} @else {{ $player->user->name }} @endif @if($player->id == $game->round->current_turn->player_id)&nbsp;<i class="fa fa-crown"></i>@endif</li>
                            <li style="border-top:none; padding-top:0; margin-top:0;" class="list-group-item" id="player-points-{{ $player->user->id }}"><i style="font-size:9pt">{{ $player->score() }} pont</i></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card" id="ready" @if($game->round->current_turn->winning_play==null) style="display:none;" @endif>
                <a href="#" class="btn btn-block btn-primary" id="ready-button">
                    <div class="card-body">
                        Kész <span id="timer" style="font-weight:bold;"></span>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row" id="winner-row" @if($game->round->current_turn->winning_play==null) style="display:none;" @endif>
                <div class="col-lg-6 offset-lg-3">
                    <h4 style="text-align:center;">Nyertes</h4>
                    <div class="card" id="black-card">
                        <div id="winner" class="card-body" @if($game->round->current_turn->winning_play==null) style="display:none;" @endif>
                            <h5 class="card-title" id="winner-text">
                                @if($game->round->current_turn->winning_play!=null)
                                    @foreach(json_decode($black_card->text) as $key => $black_piece)
                                        {{ $black_piece }} <span class="white-text">@if(isset($game->round->current_turn->winning_play->cards()->get()->toArray()[$key])){{ implode('',json_decode($game->round->current_turn->winning_play->cards()->get()->toArray()[$key]['text'])) }}@endif</span>
                                    @endforeach
                                @endif
                            </h5>
                            <h6 class="card-subtitle" id="winner-name" style="text-align:right;">
                                @if($game->round->current_turn->winning_play!=null)
                                    {{ $game->round->current_turn->winning_play->player->user->name }}
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <h4 style="text-align:center;" id="title-text">@if($game->round->current_turn->winning_play==null) Beadások @else Többi beadás @endif</h4>
            <div class="row">
                @foreach($turn->plays as $play)
                    @if($game->round->current_turn->winning_play==null || $game->round->current_turn->winning_play->id != $play->id)
                        <div class="col-lg-6" id="play-block-{{ $play->id }}">
                            <div class="card" id="black-card">
                                <div class="card-body" @if($play->player->user->id == Auth::id()) style="background-color:rgba(255,255,255,0.2);" @endif>
                                    <h5 class="card-title">
                                        @foreach(json_decode($black_card->text) as $key => $black_piece)
                                            {{ $black_piece }} <span class="white-text">@if(isset($play->cards()->get()->toArray()[$key])){{ implode('',json_decode($play->cards()->get()->toArray()[$key]['text'])) }}@endif</span>
                                        @endforeach
                                    </h5>
                                    <h6 class="card-subtitle" style="text-align:right">{{ $play->player->user->name }}</h6>
                                </div>
                                <div class="card-footer">
                                    @if($play->player->user->id !== Auth::id())
                                        <button style="display:none;" id="like-button-{{ $play->id }}" class="btn btn-default" type="button" onclick="like({{ $play->id }})">
                                            <i class="fa fa-thumbs-up"></i>
                                        </button>
                                    @endif
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
        let plays = [
            @foreach($turn->plays as $play)
            '{{ $play->id }}',
            @endforeach
        ];
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

        let pusher = new Pusher($('#pusher').val(), {
            cluster: 'eu',
            forceTLS: true
        });

        let slug = $('#slug').val();

        let channel = pusher.subscribe('game-' + slug);

        channel.bind('player-ready', function(data) {
            let player = $('#player-' + data.message).html();
            player += ` <i class="fa fa-check"></i>`;
            $('#player-' + data.message).html(player);
            $('audio#pop')[0].currentTime = 0;
            $('audio#pop')[0].play();
        });

        channel.bind('finished-game', function(data){
            window.location = data.message;
        });

        channel.bind('new-turn', function(data){
            window.location = $('#go-to-url').val();
        });

        channel.bind('turn-finished', function(data){
            let winning_id = data.message.id;
            let winning_text = data.message.text;
            let winning_name = data.message.name;
            let time_left = parseInt(data.message.time_left)*1000;

            plays.forEach(function(element){
                $('#like-button-' + element).css('display','inline');
            });

            $('#winner').css('display','block');
            $('#winner-row').css('display','block');
            $('#winner-text').html(winning_text);
            $('#winner-name').html(winning_name);

            $('#play-block-' + winning_id).css('display','none');
            $('#title-text').html('Többi beadás');
            $('#ready').css('display','block');
            $('#player-points-' + data.message.player_id).html("<i style='font-size:9pt'>" + data.message.winner_points + " pont</i>");
            if(data.messaage.ready != true){
                startCountdown(time_left);
            }
        });

        function startCountdown(time_left) {
            setInterval(function(){
                $('#timer').html(time_left/1000);
                console.log(time_left);
                time_left = time_left-1000;
                if(time_left<5000){
                    $('audio#pop')[0].currentTime = 0;
                    $('audio#pop')[0].play();
                }
            }, 1000);
            setTimeout(function(){
                $('#ready-button').click();
                $('#ready-button').removeClass('btn-primary');
                $('#ready-button').addClass('btn-default');
            }, time_left+1000);
        }

        channel.bind('start-load', function(){
            $('#waiting-for-game').modal('show');
        });

        channel.bind('play-like', function(data) {
            $('#play-' + data.message.id).html(data.message.likes + " lájk");
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
            plays.forEach(function(element){
                if(element != id){
                    $('#like-button-' + element).css('display','none');
                }
            });
        }
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
        @if($time_left<=0 && !Auth::user()->player()->ready)
            $('#ready-button').click();
        @endif
        @if($time_left<time()-10 && !Auth::user()->player()->ready)
            let time_left = parseInt({{ $time_left }})+1;
            setInterval(function(){
                time_left--;
                $('#timer').html(time_left);
                console.log(time_left);
            }, 1000);
            setTimeout(function(){
                $('#ready-button').click();
            }, (time_left+1)*1000);
        @endif
    </script>
@endpush
