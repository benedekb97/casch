@extends('layouts.admin')

@section('admin.games.active','active')

@section('title','Játék')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h5 class="card-title card-title-no-margin">Információk</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Játékosok</th>
                                <td>{{ $game->getPlayers()->count() }}</td>
                            </tr>
                            <tr>
                                <th>Státusz</th>
                                <td>
                                    @if($game->deleted_at != null)
                                        Vége - {{ $game->deleted_at->diffForHumans() }}
                                    @elseif($game->started)
                                        Játék közben
                                    @else
                                        Lobby-ban
                                    @endif
                                </td>
                            </tr>
                            @if($game->started && $game->deleted_at==null)
                                <tr>
                                    <th>Kör</th>
                                    <td>{{ $game->rounds()->count() }}/{{ $game->number_of_rounds }}</td>
                                </tr>
                                <tr>
                                    <th>Játék</th>
                                    <td>{{ $game->round->turns()->count() }}/{{ $game->players()->count() }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h5 class="card-title card-title-no-margin">Játékosok</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($game->getPlayers() as $player)
                            <li class="list-group-item">
                                {{ $player->user->name }}
                                @if($game->host->id == $player->user->id)
                                    <i data-toggle="tooltip" title="Játékvezető" class="fa fa-crown"></i>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title card-title-no-margin">Műveletek</h5>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.games.plays', ['game' => $game]) }}">Beadások</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.games.players', ['game' => $game]) }}">Játékosok</a>
                        </li>
                        @if($game->deleted_at == null)
                            <li class="nav-item text-danger">
                                <a class="nav-link bg-danger" data-toggle="modal" data-target="#delete" href="#">Játék törlése</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    @if($game->deleted_at == null)
        <div class="modal fade" id="delete">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Játék törlése</h4>
                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Biztosan törlöd ezt a játékot?</p>
                        @if($game->started)
                            <p style="margin-bottom:0;"><i>A játék már elkezdődött</i></p>
                            <p style="margin-bottom:0">Kör: {{ $game->rounds()->count() }}/{{ $game->number_of_rounds }}</p>
                            <p>Játék: {{ $game->round->turns()->count() }}/{{ $game->players()->count() }}</p>
                        @endif
                        <p style="margin-bottom:0;">{{ $game->getPlayers()->count() }} játékos játszik benne:</p>
                        <ul>
                            @foreach($game->getPlayers() as $player)
                                <li style="color:black; font-style:italic;">{{ $player->user->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Mégse <i class="fa fa-check"></i></button>
                        <a href="{{ route('admin.games.delete', ['game' => $game]) }}" class="btn btn-danger">Törlés <i class="fa fa-trash"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endpush
