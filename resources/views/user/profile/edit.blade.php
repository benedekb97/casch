@extends('layouts.main')

@section('title','Profil szerkesztése')

@section('content')
    <h2 class="page-header with-description">Profil szerkesztése</h2>
    <h2 class="page-description">
        <a href="{{ route('user.profile') }}">Vissza</a>
    </h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Szerkesztés</h4>
                </div>
                <form action="{{ route('user.profile.save') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <div class="form-group">
                            <div class="input-group">
                            <span class="input-group-prepend">
                                <label class="input-group-text" for="nickname">Becenév</label>
                            </span>
                                <input type="text" name="nickname" id="nickname" value="{{ $user->nickname }}" placeholder="Becenév" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('user.profile') }}" class="btn btn-sm btn-default">
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
@endsection
