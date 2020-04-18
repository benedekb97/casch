@extends('layouts.admin')

@section('title','Játékok')

@section('admin.games.active','active')

@section('content')
    <div class="row">
        <div class="col-md-12" style="margin-bottom:15px;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Játékok</h4>
                </div>
            </div>
            <div class="row justify-content-center" style="margin-top:15px;">
                <?php
                if($page == null || $page == 0){
                    $page = 1;
                }
                ?>
                @if($page>1)
                    <a href="{{ route('admin.games.index', ['page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('admin.games.index', ['page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('admin.games.index', ['page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('admin.games.index', ['page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
        </div>

        @foreach($games as $game)
            <div class="col-md-4" style="margin-bottom:15px;">
                <div class="card">
                    <a href="{{ route('admin.games.view', ['game'=> $game]) }}" class="card-link-black">
                        <div class="card-body">
                            <h4 class="card-title card-title-no-margin">{{ $game->created_at }}</h4>
                            <p style="margin-bottom:0;">{{ $game->getPlayers()->count() }} játékos</p>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
        <div class="col-md-12">
            <div class="row justify-content-center" style="margin-top:15px;">
                <?php
                if($page == null || $page == 0){
                    $page = 1;
                }
                ?>
                @if($page>1)
                    <a href="{{ route('admin.games.index', ['page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('admin.games.index', ['page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('admin.games.index', ['page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('admin.games.index', ['page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
        </div>
    </div>
@endsection
