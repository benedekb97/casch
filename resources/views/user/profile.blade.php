@extends('layouts.main')

@section('title','Profilom')

@section('content')
    <h2 class="page-header with-description">Profilom</h2>
    <h2 class="page-description">
        <a href="{{ route('index') }}">Vissza</a>
    </h2>
    <div class="row" style="margin-bottom:15px;">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-top">{{ $user->name }}</h4>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('user.profile') }}" class="nav-link active">Információk</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.profile.edit') }}" class="nav-link">Szerkesztés</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.decks') }}" class="nav-link">Paklik</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title card-title-no-margin">Információk</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Becenév</th>
                                @if($user->nickname)
                                    <td>{{ $user->nickname }}</td>
                                @else
                                    <td><i>Nincs beállítva</i></td>
                                @endif
                            </tr>
                            <tr>
                                <th>Saját paklik</th>
                                <td>{{ $user->decks()->count() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
