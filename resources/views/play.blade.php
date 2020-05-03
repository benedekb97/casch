@extends('layouts.main')

@section('title','Cards Against Schönherz')

@section('content')
    <audio id="pop">
        <source src="{{ asset('audio/pop.mp3') }}" type="audio/mpeg">
    </audio>
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <input type="hidden" id="get_data_url" value="{{ route('game.data', ['game' => $game]) }}">
    <input type="hidden" id="submit_url" value="{{ route('game.submit', ['game' => $game]) }}">
    <input type="hidden" id="user_id" value="{{ Auth::id() }}">
    <input type="hidden" id="player_id" value="{{ Auth::user()->player()->id }}">
    <input type="hidden" id="cards_needed" value="">
    <input type="hidden" id="answers_given" value="0">
    <input type="hidden" id="slug" value="{{ $game->slug }}">
    <input type="hidden" id="pusher" value="{{ env('PUSHER_APP_KEY') }}">
    @if(Auth::user()->player()->id == $game->round->current_turn->host->id)
        <input type="hidden" id="choose_winner_url" value="{{ route('game.choose_turn_winner', ['game' => $game]) }}">
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">Kör: {{ $game->round->number }}/{{ $game->number_of_rounds }}<br>Játék: {{ $game->round->turns()->count() }}/{{ $game->players()->count() }}</div>
                <div class="card-body" id="players"></div>
                <div class="card-footer">
                    @if($game->round->current_turn->host->user->id !== Auth::id())
                        <span>
                            <a style="margin-bottom:10px;" href="#" class="btn btn-default" data-toggle="modal" data-target="#leave-game">
                                Kilépés <i class="fa fa-door-open"></i>
                            </a>
                        </span>
                    @endif
                    @if(!Auth::user()->player()->voted)
                        <span id="vote-kick-button" style="margin-bottom:10px;">
                            <a style="margin-bottom:10px;" href="#" class="btn btn-default" id="vote-kick-link" data-toggle="modal" data-target="#kick-modal">
                                Játékos kirugása <i class="fa fa-head-side-cough-slash"></i>
                            </a>
                        </span>
                    @endif
                    <span id="submit-button" style="display:none; margin-bottom:10px;">
                        <a style="margin-bottom:10px;" href="#" class="btn btn-primary" id="submit-link">
                            Mentés <i class="fa fa-save"></i>
                        </a>
                    </span>
                    <span id="reset-button" style="display:none; margin-bottom:10px;">
                        <a style="margin-bottom:10px;" href="#" class="btn btn-warning">
                            Visszaállítás <i class="fa fa-refresh"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row justify-content-md-center">
                <div class="col-xl-4">
                    <div class="card" id="black-card"></div>
                </div>
            </div>
            <div class="row justify-content-md-center" id="answers"></div>
            <div class="row" id="white-cards"></div>
        </div>
    </div>
    <input type="hidden" id="site-index" value="{{ route('index') }}">
    @include('chat')
@endsection

@push('scripts')
    <script src="{{ asset('js/play.js') }}"></script>
    <script src="{{ asset('js/chat.js') }}"></script>
@endpush

@push('modals')
    @if(!Auth::user()->player()->voted)
        <input type="hidden" id="vote-kick-url" value="{{ route('game.vote_kick', ['slug' => $game->slug]) }}">
        <div class="modal fade" id="kick-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Játékos kirugása</h4>
                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Egyszerre csak egy játékosra szavazhatsz! Ha ki is lesz baszva a körben akkor újra szavazhatsz. Ha nem, a következő körben szavazhatsz újra</p>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label for="kick-player" class="input-group-text">Játékos</label>
                                </div>
                                <select id="kick-player" class="form-control">
                                    <option selected disabled>Válassz egyet!</option>
                                    @foreach(Auth::user()->game()->players as $player)
                                        @if(Auth::user()->player()->id !== $player->id && $game->round->current_turn->player_id !== $player->id)
                                            <option value="{{ $player->id }}">{{ $player->user->nickname ?: $player->user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="votekick">
                            Szavazás <i class="fa fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Mégse <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="waiting-for-host">
        <div class="modal-dialog" style="padding:0; margin:0;">
            <div class="modal-content" style="background:rgba(0,0,0,0.5); width:100vw; height:100vh;">
                <h2 style="text-align:center; margin-top:45vh; margin-bottom:0">Várakozás <i class="fa fa-spin fa-wheelchair"></i></h2>
                <h3 style="text-align:center;  margin-top:0; padding:5px;">Most dől el, hogy ki nyert!</h3>
            </div>
        </div>
    </div>
    @if($game->round->current_turn->host->user->id !== Auth::id())
        <div class="modal fade" id="leave-game">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Biztos kilépsz?</h4>
                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Ha most kilépsz a játékból nem fogsz tudni visszalépni!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Mégse <i class="fa fa-times"></i>
                        </button>
                        <a href="{{ route('game.leave') }}" class="btn btn-danger">
                            Kilépek <i class="fa fa-door-open"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endpush
