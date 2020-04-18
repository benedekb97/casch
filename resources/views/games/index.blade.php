@extends('layouts.main')

@section('title','Eddigi játékaim')

@section('content')
    <h2>Eddigi játékaim &raquo; <a href="{{ route('index') }}">Főoldal</a></h2>
    <div class="row">
        <div class="col-md-12">
            <div class="row justify-content-center" style="margin-bottom:15px;">
                <?php
                if($page == null || $page == 0){
                    $page = 1;
                }
                ?>
                @if($page>1)
                    <a href="{{ route('games.index', ['page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('games.index', ['page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('games.index', ['page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('games.index', ['page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
        </div>
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
        <div class="col-md-12">
            <div class="row justify-content-center" style="margin-bottom:15px;">
                <?php
                if($page == null || $page == 0){
                    $page = 1;
                }
                ?>
                @if($page>1)
                    <a href="{{ route('games.index', ['page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('games.index', ['page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('games.index', ['page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('games.index', ['page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
        </div>
    </div>
@endsection
