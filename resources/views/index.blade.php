@extends('layouts.main')

@section('title','Cards against Schönherz')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('help') }}" class="btn btn-block btn-lg @auth btn-info @else btn-default @endauth" style="cursor:help">Segítség!</a>
        </div>
        @auth
            <div class="col-md-4">
                <a href="{{ route('logout') }}" class="btn btn-block btn-lg btn-danger">Kijelentkezés</a>
            </div>
            @if(Auth::user()->player() == null)
                @can('host')
                    <div class="col-md-4">
                        <a href="{{ route('host') }}" class="btn btn-block btn-lg btn-success">Új játék</a>
                    </div>
                @endcan
            @else
                <div class="col-md-4">
                    <a href="{{ route('join', ['slug' => Auth::user()->game()->slug]) }}" class="btn btn-block btn-lg btn-success"><b>Visszacsatlakozás</b></a>
                </div>
            @endif
            @group('admin')
                <div class="col-md-4">
                    <a href="{{ route('admin.index') }}" class="btn btn-block btn-lg btn-warning">Admin</a>
                </div>
            @endgroup
            <div class="col-md-4">
                <a href="{{ route('games.index') }}" class="btn btn-block btn-lg btn-default">Eddigi játékaim</a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('user.profile') }}" class="btn btn-block btn-lg btn-default">Profilom</a>
            </div>
        @else
            <div class="col-md-4">
                <a href="{{ route('login') }}" class="btn btn-block btn-lg btn-success">Bejelentkezés</a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('register') }}" class="btn btn-block btn-lg btn-info">Regisztráció</a>
            </div>
            @if($_SERVER['REMOTE_ADDR']=='192.168.0.1')
                <div class="col-md-4">
                    <a href="{{ route('test') }}" class="btn btn-block btn-lg btn-default">TEST</a>
                </div>
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="file" name="file">
                    <input type="submit">
                </form>
            @endif
        @endauth
    </div>
    @auth
    <div class="row">
        <div class="col-md-4" style="margin-bottom:15px;">
            <div class="card">
                 <div class="card-header">
                     <h4 class="card-title card-title-no-margin">Random statisztikák</h4>
                 </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Játékok száma</th>
                                <td>{{ number_format($games_count,0, ',', ' ') }} db</td>
                            </tr>
                            <tr>
                                <th>Játékosok száma</th>
                                <td>{{ number_format($players_count,0, ',', ' ') }} db</td>
                            </tr>
                            <tr>
                                <th>Kombinációk száma</th>
                                <td>{{ number_format($plays_count,0, ',', ' ') }} db</td>
                            </tr>
                            <tr>
                                <th>Játékok ma</th>
                                <td>{{ $games_today }} db</td>
                            </tr>
                            <tr>
                                <th rowspan="2" style="vertical-align: middle">Leggyakoribb kártya</th>
                                <td>{{ $most_played_card->getTextWhite() }}</td>
                            </tr>
                            <tr>
                                <td>{{ number_format($most_played,0, ',', ' ') }}-szer</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h4 class="card-title"><b>Kiemelkedő minőségű kártyák</b></h4>
                </div>
            </div>
            <div class="row">
                @foreach($featured_plays as $play)
                    <div class="col-md-4">
                        @if(in_array(Auth::id(),$play->getUsers()))
                            <a class="card-link-black" href="{{ route('game.finished', ['slug' => $play->getGame()->slug]) }}">
                                @endif
                                <div class="card" id="black-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{!! $play->getTextHTML() !!}</h5>
                                        <p style="text-align:right; font-size:10pt;">{{ $play->getPlayer()->user->nickname ? $play->getPlayer()->user->nickname : $play->getPlayer()->user->name }}</p>
                                    </div>
                                </div>
                                @if(in_array(Auth::id(),$play->getUsers()))
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endauth
@endsection

@push('modals')
    @if($butthurt)
        <div class="modal fade" id="front-message">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Hey!</h4>
                        <button class="close" type="button" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Jelezték páran, hogy találtak olyan kártyát, vagy kártyák kombinációját ami számukra sértő.</p>
                        <p>A <a href="{{ route('disclaimer') }}">discalimer</a>-ben sok minden le van írva, köztük az is, hogy ezeken nem éri meg megsértődni, mert faszságok.</p>
                        <p>Ha ennek ellenére úgy érzed, hogy valami konkrétan ellened irányult, tudod jelezni felénk <a href="{{ route('complaint') }}">ezen a linken</a>.</p>
                        <p>További szép napokat és jó játékot!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Bezárás</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('#front-message').modal('toggle');
        </script>
    @endif
@endpush
