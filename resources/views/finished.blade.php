@extends('layouts.main')

@section('title','Játék vége')

@section('content')
{{--    {{ var_dump($points) }}--}}
    @foreach($points as $point)
        {{ $point['name'] }} => {{ $point['points'] }}<br>
    @endforeach
@endsection
