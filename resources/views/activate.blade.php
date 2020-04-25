@extends('layouts.main')

@section('title','Aktiváld felhasználódat!')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title card-title-no-margin">Felhasználó aktiválása</h4>
                </div>
                <div class="card-body">
                    <p>Küldtünk egy emailt a regisztrációkor megadott email címedre, amivel tudod aktviálni felhasználódat.</p>
                    <p>Ha nem kaptad meg kattints <a href="{{ route('activate.resend') }}">erre a linkre</a>, és újra elküldjük!</p>
                    <p>Ha akkor sem jön meg, nézd meg a spam mappádban, ha ott sincs akkor küldj egy emailt a <a href="mailto:cards.against.sch@gmail.com">cards.against.sch@gmail.com</a> email címre arról az emailről amit megadtál regisztrációkor!</p>
                </div>
            </div>
        </div>
    </div>
@endsection
