@extends('layouts.main')

@section('title','Játék vége')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h5 class="card-title">Játékosok</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($points as $key => $point)
                            <li class="list-group-item" style="
                                @if($key == 0) background:gold; color:black; @elseif($key == 1)
                                background:silver; color:black; @elseif($key == 2)
                                background:#cd7f32; color:black; @endif
                                border-bottom:0; margin-bottom:0; padding-bottom:0;"><b @if($key>=0 && $key<=2) style="color:black;" @endif>{{ $key+1 }}.</b> {{ $point['name'] }}</li>
                            <li class="list-group-item" style="
                                @if($key == 0) background:gold; color:black; @elseif($key == 1)
                                background:silver; color:black; @elseif($key == 2)
                                background:#cd7f32; color:black; @endif
                                border-top:0; margin-top:0; padding-top:0;"><i style="font-size:10pt; @if($key>=0 && $key<=2) color:black; @endif">{{ $point['points'] }} pont</i></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <a href="{{ route('games.index') }}" class="btn btn-block btn-primary btn-lg">
                Eddigi játékaim
            </a>
            <a href="{{ route('index') }}" class="btn btn-block btn-default btn-lg">
                Vissza a főoldalra
            </a>
        </div>
        <div class="col-lg-8">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h2>Játék vége</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" style="margin-bottom:15px;">
                        <div class="card-header">
                            <h5 class="card-title">Kártyák</h5>
                            <div class="row justify-content-center">
                                <?php
                                if($page == null || $page == 0){
                                    $page = 1;
                                }
                                ?>
                                @if($page>1)
                                    <a href="{{ route('game.finished', ['slug' => $game->slug, 'page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                                    <a href="{{ route('game.finished', ['slug' => $game->slug, 'page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                                @else
                                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                                @endif
                                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                                @if($page<$pages)
                                    <a href="{{ route('game.finished', ['slug' => $game->slug, 'page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                                    <a href="{{ route('game.finished', ['slug' => $game->slug, 'page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                                @else
                                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($plays as $play)
                                    <div class="col-lg-4">
                                        <div class="card" id="black-card">
                                            <div class="card-body" @if($play->points>0) style="background-color:rgba(255,255,255,0.1);" @endif>
                                                <h5 class="card-title">{!! $play->getTextHTML() !!}</h5>
                                                <p style="text-align:right; font-size:11pt; line-height:11pt;">@if($play->getPlayer()->user_id == Auth::id())<b>@endif{{ $play->getPlayer()->user->name }}@if($play->getPlayer()->user_id == Auth::id())</b>@endif</p>
                                            </div>
                                            @if($play->points==0)
                                                <div class="card-footer" style="line-height:10pt;">
                                                    <i style="font-size:10pt;">{{ $play->likes }} lájk</i>
                                                </div>
                                            @endif
                                            @if($play->featured)
                                                @can('unfeature-play')
                                                    <div class="card-footer" style="line-height:10pt">
                                                        <a style="background:gold;" class="btn btn-sm btn-default" href="{{ route('plays.unfeature', ['play' => $play->id]) }}">
                                                            <i class="fa fa-star"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                            @else
                                                @can('feature-play')
                                                    <div class="card-footer" style="line-height:10pt">
                                                        <a class="btn btn-sm btn-default" href="{{ route('plays.feature', ['play' => $play->id]) }}">
                                                            <i class="fa fa-star"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
