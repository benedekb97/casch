@extends('layouts.main')

@section('title','Felhasználó sikeresen aktiválva!')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Felhasználó aktiválva!</h4>
                </div>
                <div class="card-body">
                    <p>Sikeresen aktiváltuk felhasználódat!</p>
                    @if(Auth::check())
                        <p>
                            <a href="{{ route('index') }}">Vissza a főoldalra</a>
                        </p>
                    @else
                        <p>
                            <a href="{{ route('login') }}">Jelentkezz be!</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
