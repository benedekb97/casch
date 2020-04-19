@extends('layouts.main')

@section('title', 'Pakli - '.$deck->name)

@section('content')
    <h2 class="page-header with-description">{{ $deck->name }}</h2>
    <h2 class="page-description">
        <a href="{{ route('user.decks') }}">Vissza a paklikhoz</a>
    </h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title card-top">Pakli</h5>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('user.decks.view', ['deck' => $deck]) }}">Információk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.decks.deck.white', ['deck' => $deck]) }}">Fehér kártyák</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.decks.deck.black', ['deck' => $deck]) }}">Fekete kártyák</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-toggle="modal" data-target="#import">Pakli behúzása</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Információk</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Fehér kártyák</th>
                                <td>{{ $deck->whiteCards()->count() }}</td>
                            </tr>
                            <tr>
                                <th>Fekete kártyák</th>
                                <td>{{ $deck->blackCards()->count() }}</td>
                            </tr>
                            <tr>
                                <th>Játékok amiben használták</th>
                                <td>{{ $deck->games()->withTrashed()->count() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="import">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pakli importálása</h4>
                    <button class="close" data-dismiss="modal" type="button">&times;</button>
                </div>
                <form action="{{ route('user.decks.deck.import', ['deck' => $deck]) }}" method="POST">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>Ez csak a pakliban aktuálisan lévő kártyákat húzza be, ha változik a pakli később a tiéd nem fog!</p>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="deck" class="input-group-text">Pakli</label>
                                </span>
                                <select name="deck" id="deck" class="form-control" required>
                                    <option disabled selected>Válassz egyet!</option>
                                    @foreach($decks as $deck)
                                        <option value="{{ $deck->id }}">{{ $deck->name }} - {{ $deck->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                            Mégse <i class="fa fa-times"></i>
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            Importálás <i class="fa fa-file-import"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
