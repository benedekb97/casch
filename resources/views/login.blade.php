@extends('layouts.main')

@section('title','Bejelentkezés')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Bejelentkezés</h4>
                </div>
                <form method="POST">
                    {{ csrf_field() }}
                    <div class="card-body" style="padding-bottom:0;">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label class="input-group-text" for="email">Email cím</label>
                                </span>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="{{ $email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label class="input-group-text" for="password">Jelszó</label>
                                </span>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Jelszó">
                            </div>
                        </div>
                        @if($error)
                            <div class="alert alert-danger alert-dismissible bg-danger text-white border-danger">
                                <p style="margin-bottom:0">
                                    <i class="fa fa-exclamation-triangle"></i> {{ $error }}
                                </p>
                                <button class="close" data-dismiss="alert" type="button">&times;</button>
                            </div>
                        @endif
                        <div class="form-group">
                            <div class="input-group">
                                <button style="margin-bottom:0" type="submit" class="btn btn-primary btn-block">
                                    Bejelentkezés <i class="fa fa-door-open"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('redirect') }}" class="btn btn-default btn-block">
                            Bejelentkezés AuthSCH-val <img style="width:25px; margin-left:3px;" src="{{ asset('img/logo_vektor.png') }}" alt="SCH">
                        </a>
                        <a href="{{ route('index') }}" class="btn btn-default btn-block">
                            Vissza a főoldalra
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
