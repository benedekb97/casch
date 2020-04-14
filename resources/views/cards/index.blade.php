@extends('layouts.main')

@section('title','Kártyák')

@section('content')
    <h3>
        <a href="{{ route('index') }}">Vissza</a>
    </h3>
    <div class="row">
        <div class="col-md-6">
            <a href="{{ route('cards.black') }}" class="btn btn-default btn-block btn-lg">Fekete kártyák</a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('cards.white') }}" class="btn btn-default btn-block btn-lg">Fehér kártyák</a>
        </div>
    </div>
@endsection
