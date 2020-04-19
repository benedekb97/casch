@extends('layouts.main')

@section('title', 'Új pakli létrehozása')

@section('content')
    <h2 class="page-header with-description">Új pakli létrehozása</h2>
    <h2 class="page-description">
        <a href="{{ route('user.decks') }}">Vissza</a>
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
                            <a href="{{ route('user.decks') }}" class="nav-link">Összes</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.decks.create') }}" class="nav-link active">Új létrehozása</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title-no-margin card-title">Új pakli</h4>
                        </div>
                        <form action="{{ route('user.decks.save') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-prepend">
                                        <label for="name" class="input-group-text">Név</label>
                                    </span>
                                        <input type="text" name="name" id="name" placeholder="Név" class="form-control">
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="public" name="public">
                                    <label for="public" class="form-check-label">Bárki használhatja</label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('user.decks') }}" class="btn btn-sm btn-default">
                                    Mégse <i class="fa fa-times"></i>
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Mentés <i class="fa fa-save"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
