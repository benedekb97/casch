@extends('layouts.main')

@section('title','Regisztráció')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Regisztráció</h4>
                </div>
                <form method="POST">
                    <div class="card-body">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="email" class="input-group-text">
                                        Email cím<span style="color:red;font-style:italic;">*</span>
                                    </label>
                                </span>
                                <input id="email" class="form-control" type="email" name="email" placeholder="Email cím" required value="{{ $email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="name" class="input-group-text">
                                        Teljes név<span style="color:red;font-style:italic;">*</span>
                                    </label>
                                </span>
                                <input id="name" name="name" type="text" placeholder="Név" class="form-control" required value="{{ $name }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="nickname" class="input-group-text">Becenév</label>
                                </span>
                                <input id="nickname" type="text" name="nickname" placeholder="Becenév" class="form-control" value="{{ $nickname }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="password1" class="input-group-text">
                                        Jelszó<span style="color:red;font-style:italic;">*</span>
                                    </label>
                                </span>
                                <input id="password1" name="password1" type="password" placeholder="Jelszó" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="password2" class="input-group-text">
                                        Jelszó megint<span style="color:red;font-style:italic;">*</span>
                                    </label>
                                </span>
                                <input id="password2" name="password2" type="password" placeholder="Jelszó megint" class="form-control" required>
                            </div>
                        </div>
                        @if($error)
                            <div class="alert alert-dismissible alert-danger bg-danger text-white border-danger">
                                <p style="margin-bottom:0">
                                    <i class="fa fa-exclamation-triangle"></i> {{ $error }}
                                </p>
                                <button class="close" data-dismiss="alert" type="button">&times;</button>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            Regisztrálok!
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
