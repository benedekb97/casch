@extends('layouts.main')

@section('title','Saját paklik')

@section('content')
    <h2 class="page-header with-description">Saját paklik</h2>
    <h2 class="page-description">
        <a href="{{ route('user.profile') }}">Vissza</a>
    </h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h4 class="card-title card-top">{{ $user->name }}</h4>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('user.profile') }}" class="nav-link">Információk</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.profile.edit') }}" class="nav-link">Szerkesztés</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.decks') }}" class="nav-link active">Paklik</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title card-top">Paklik</h5>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('user.decks') }}" class="nav-link active">Összes</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.decks.create') }}" class="nav-link">Új létrehozása</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                @foreach($decks as $deck)
                    <div class="col-md-4">
                        <div class="card">
                            <a href="{{ route('user.decks.view', ['deck' => $deck]) }}" class="card-link-black">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $deck->name}}</h4>
                                    <p style="margin-bottom:0">{{ $deck->whiteCards()->count() }} fehér kártya</p>
                                    <p style="margin-bottom:0">{{ $deck->blackCards()->count() }} fekete kártya</p>
                                    <p>{{ $deck->games()->withTrashed()->count() }} játékban használva</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
