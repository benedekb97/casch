@extends('layouts.main')

@section('title','Cards Against Schönherz')

@section('content')
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <input type="hidden" id="get_data_url" value="{{ route('game.data', ['game' => $game]) }}">
    <input type="hidden" id="user_id" value="{{ Auth::id() }}">
    <input type="hidden" id="player_id" value="{{ Auth::user()->player()->id }}">
    <input type="hidden" id="cards_needed" value="">
    <input type="hidden" id="answers_given" value="0">
    <div class="row">
        <div class="col-md-4">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-body" id="players"></div>
            </div>
            <div class="card" id="submit-button" style="display:none;">
                <a href="#" class="btn btn-primary btn-block" id="submit-link">
                    <div class="card-body" style="font-size:15pt;">
                        Mentés
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row justify-content-md-center">
                <div class="col-md-4">
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
