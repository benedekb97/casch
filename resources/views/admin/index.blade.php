@extends('layouts.admin')

@section('title','Főoldal')

@section('admin.index.active','active')

@section('content')
    <div class="row">
        <div class="col-lg-12" style="margin-bottom:15px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title card-title-no-margin">Aktív játékok</h5>
                </div>
            </div>
        </div>
        @foreach($active_games as $game)
            <div class="col-lg-6" style="margin-bottom:15px;">
                <div class="card" id="black-card">
                    <a class="card-link-black" href="{{ route('admin.games.view', ['game' => $game]) }}">
                        <div class="card-body">
                            <h6 class="card-title"><i>{{ $game->created_at }}</i></h6>
                            <p style="margin-bottom:0;">{{ $game->players() !== null ? $game->players()->count() : 0 }} játékos</p>
                            @if($game->rounds() !== null && $game->rounds()->count() == 0)
                                <p style="margin-bottom:0;">Még nem kezdődött el</p>
                            @else
                                <p style="margin-bottom:0">Kör: {{ $game->rounds() !== null ? $game->rounds()->count() : 0 }}/{{ $game->number_of_rounds }}</p>
                                <p style="margin-bottom:0">Játék: {{ $game->round !== null && $game->round->turns() !== null ? $game->round->turns()->count() : 0 }}/{{ $game->players() !== null ? $game->players()->count() : 0 }}</p>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
