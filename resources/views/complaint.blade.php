@extends('layouts.main')

@section('title','Kártya jeelntése')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-3" style="margin-bottom:15px;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Kártya jelentése</h4>
                </div>
                <form method="POST">
                    {{ csrf_field() }}
                    <div class="card-body" style="padding-bottom:0;">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="card" class="input-group-text">Kártya/Kártya kombi<span style="color:rgba(255,255,255,0.7)">náci</span>ó</label>
                                </span>
                                <input type="text" name="card" id="card" class="form-control" placeholder="Kártya">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="message" class="input-group-text">Magyarázat</label>
                                </span>
                                <input type="text" name="message" id="message" class="form-control" placeholder="Miért sértő?">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Mentés <i class="fa fa-save"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
