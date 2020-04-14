@extends('layouts.main')

@section('title','Fehér kártyák')

@section('content')
    <h2>Fehér kártyák - <button class="btn btn-default btn-lg" data-toggle="modal" data-target="#add-card"><i class="fa fa-plus"></i> Új kártya</button></h2>
    <h3><a href="{{ route('cards.index') }}">Vissza</a></h3>
    <div class="row">
        @foreach($cards as $card)
            <div class="col-md-3">
                <div class="card card-white">
                    <div class="card-body">
                        <button style="color:white;" class="close" type="button" data-toggle="modal" data-target="#delete-card-{{ $card->id }}">&times;</button>
                        <h5 class="card-title">{{ implode("___", json_decode($card->text)) }}</h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-xl-12">
            {{ $cards->links() }}
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="add-card">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Új kártya</h4>
                    <button class="close" data-dismiss="modal" type="button">&times;</button>
                </div>
                <form action="{{ route('cards.add') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="type" value="white">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="text" class="input-group-text">Szöveg</label>
                                </span>
                                <input type="text" class="form-control" id="text" name="text" placeholder="Szöveg">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Mégse</button>
                        <input type="submit" class="btn btn-primary" value="Mentés">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @foreach($cards as $card)
        <div class="modal fade" id="delete-card-{{ $card->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Kártya törlése</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Biztosan törli ezt a kártyát?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Mégse</button>
                        <a href="{{ route('cards.delete', ['card' => $card]) }}" class="btn btn-primary">Törlés</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endpush
