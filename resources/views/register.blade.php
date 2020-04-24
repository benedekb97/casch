@extends('layouts.main')

@section('title','Regisztráció')

@section('content')
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        {{ csrf_field() }}
                        <input type="text" class="form-control" name="email" placeholder="Email cím">
                        <input type="password" class="form-control" name="password" placeholder="Jelszó">
                        <input type="text" class="form-control" name="name" placeholder="Név">
                        <input type="submit" value="Regisztrálok" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
