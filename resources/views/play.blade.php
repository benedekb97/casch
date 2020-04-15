@extends('layouts.main')

@section('title','Cards Against Schönherz')

@section('content')
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <input type="hidden" id="get_data_url" value="{{ route('game.data', ['game' => $game]) }}">
    <input type="hidden" id="submit_url" value="{{ route('game.submit', ['game' => $game]) }}">
    <input type="hidden" id="user_id" value="{{ Auth::id() }}">
    <input type="hidden" id="player_id" value="{{ Auth::user()->player()->id }}">
    <input type="hidden" id="cards_needed" value="">
    <input type="hidden" id="answers_given" value="0">
    <input type="hidden" id="slug" value="{{ $game->slug }}">
    @if(Auth::user()->player()->id == $game->round->current_turn->host->id)
        <input type="hidden" id="choose_winner_url" value="{{ route('game.choose_turn_winner', ['game' => $game]) }}">
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">Kör: {{ $game->round->number }}/{{ $game->number_of_rounds }}</div>
                <div class="card-body" id="players"></div>
            </div>
            <div class="card" id="submit-button" style="display:none; margin-bottom:15px;">
                <a href="#" class="btn btn-primary btn-block" id="submit-link">
                    <div class="card-body" style="font-size:15pt;">
                        Mentés <i class="fa fa-save"></i>
                    </div>
                </a>
            </div>
            <div class="card" id="reset-button" style="display:none;">
                <a href="#" class="btn btn-warning btn-block">
                    <div class="card-body" style="font-size:15pt">
                        Visszaállítás <i class="fa fa-refresh"></i>
                    </div>
                </a>
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
@endsection

@push('scripts')
    <script src="{{ asset('js/play.js') }}"></script>
@endpush

@push('modals')
    <div class="modal fade" id="waiting-for-host">
        <div class="modal-dialog" style="padding:0; margin:0;">
            <div class="modal-content" style="background:rgba(0,0,0,0.5); width:100vw; height:100vh;">
                <h2 style="text-align:center; margin-top:45vh; margin-bottom:0">Várakozás <i class="fa fa-spin fa-wheelchair"></i></h2>
                <h3 style="text-align:center;  margin-top:0; padding:5px;">Most dől el, hogy ki nyert!</h3>
            </div>
        </div>
    </div>
@endpush
