@extends('layouts.main')

@section('title','Hibajelentés')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="border-bottom:1px solid rgba(255,255,255,0.1)">
                    <h4 class="card-title card-title-no-margin">Hibajelentés</h4>
                </div>
                <form action="{{ route('report.submit') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label for="page" class="input-group-text">Oldal<span style="color:red">*</span></label>
                                </div>
                                <input required class="form-control" name="page" type="text" id="page" placeholder="Oldal">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label for="description" class="input-group-text">Leírás<span style="color:red">*</span></label>
                                </div>
                                <textarea required class="form-control" id="description" name="description" placeholder="Hiba leírása"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label for="trace" class="input-group-text">Stack Trace</label>
                                </div>
                                <textarea class="form-control" id="trace" name="trace" placeholder="Stack Trace"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="border-top:1px solid rgba(255,255,255,0.1); margin-top:-20px;">
                        <button type="submit" class="btn btn-primary">
                            Mentés <i class="fa fa-save"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
