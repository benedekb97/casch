@extends('layouts.admin')

@section('admin.games.active','active')
@section('title','Játékos » '.$player->user->name)

@section('content')
    <div class="row">
        <div class="col-md-8" style="margin-bottom:15px;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Információk &raquo; <a href="{{ route('admin.games.players', ['game' => $player->game_id]) }}">Vissza</a></h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Név</th>
                                <td>{{ $player->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Pontszám</th>
                                <td>{{ $player->score() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="margin-bottom:15px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title card-title-no-margin">Műveletek</h5>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        @if($player->cards()->count()!=0)
                            <li class="nav-item">
                                <a class="nav-link bg-danger" href="#" data-toggle="modal" data-target="#deal">Kártyák újraosztása</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="margin-bottom:15px;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Kártyák</h4>
                </div>
            </div>
        </div>
        @foreach($player->cards as $card)
            <div class="col-md-3">
                <div class="card card-white">
                    <div class="card-body">
                        {{ $card->getTextWhite() }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="deal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title card-title-no-margin">Kártyák újraosztása</h4>
                    <button class="close" data-dismiss="modal" type="button">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Biztosan újraosztod ennek a játékosnak a lapjait?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Mégse <i class="fa fa-times"></i></button>
                    <a href="{{ route('admin.players.deal', ['player' => $player]) }}" class="btn btn-danger">Igen <i class="fa fa-spinner fa-spin"></i></a>
                </div>
            </div>
        </div>
    </div>
@endpush
