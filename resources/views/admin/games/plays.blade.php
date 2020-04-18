@extends('layouts.admin')

@section('admin.games.active','active')

@section('title','Beadások')

@section('content')

    <div class="row">
        <div class="col-md-12" style="margin-bottom:15px;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Beadások &raquo; <a href="{{ route('admin.games.view', ['game' => $game]) }}">Vissza</a></h4>
                </div>
            </div>
            <div class="row justify-content-center" style="margin-top:15px;">
                <?php
                if($page == null || $page == 0){
                    $page = 1;
                }
                ?>
                @if($page>1)
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
        </div>
        @foreach($plays as $play)
            <div class="col-md-4">
                <div class="card" id="black-card">
                    <div class="card-body">
                        <h5 class="card-title" style="margin-bottom:5px;">{!! $play->getTextHTML() !!}</h5>
                        <p style="margin-bottom:0; text-align:right; font-size:11pt; line-height:11pt;">{{ $play->getPlayer()->user->name }}</p>
                    </div>
                    <div class="card-footer" style="line-height:10pt;">
                        @if($play->points==0)
                            <i style="font-size:10pt;">{{ $play->likes }} lájk</i>
                        @endif
                        @if($play->featured)
                            <a style="background:gold;" class="btn btn-sm btn-default" href="{{ route('plays.unfeature', ['play' => $play->id]) }}">
                                <i class="fa fa-star"></i>
                            </a>
                        @else
                            <a class="btn btn-sm btn-default" href="{{ route('plays.feature', ['play' => $play->id]) }}">
                                <i class="fa fa-star"></i>
                            </a>
                        @endif
                    </div>
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
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('admin.games.plays', ['game' => $game, 'page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
        </div>
    </div>
@endsection
