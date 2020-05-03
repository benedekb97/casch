@extends('layouts.main')

@section('title','Nyertes kiválasztása')

@section('content')
    <input type="hidden" id="pusher" value="{{ env('PUSHER_APP_KEY') }}">
    <h2>Válassz nyertest!</h2>
    <input type="hidden" id="slug" value="{{ $game->slug }}">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card" id="black-card">
                <div class="card-body">
                    <h5 class="card-title">{{ implode('____', json_decode($black_card->text)) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach($plays as $play)
            <div class="col-md-4">
                <a class="card-link-black" href="#" data-toggle="modal" data-target="#choose-play-{{ $play->id }}">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                @foreach(json_decode($black_card->text) as $key => $black_piece)
                                    {{ $black_piece }} <span class="white-text">@if(isset($play->cards()->get()->toArray()[$key])) {{ implode('',json_decode($play->cards()->get()->toArray()[$key]['text'])) }} @endif</span>
                                @endforeach
                            </h5>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <input type="hidden" id="site-index" value="{{ route('index') }}">
    <input type="hidden" id="user_id" value="{{ Auth::id() }}">
@endsection

@push('scripts')
    <script>

        let pusher = new Pusher($('#pusher').val(), {
            cluster: 'eu',
            forceTLS: true
        });

        let slug = $('#slug').val();
        let channel = pusher.subscribe('game-' + slug);

        channel.bind('leave-game', function(data){

            if(data.message == $('#user_id').val()){
                window.location = $('#site-index').val();
            }
        });
    </script>
@endpush

@push('modals')
    @foreach($plays as $play)
        <div class="modal fade" id="choose-play-{{ $play->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Nyertes választása</h4>
                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Biztos ezt választod nyertesnek?</p>
                        <p style="font-style:italic;">
                            @foreach(json_decode($black_card->text) as $key => $black_piece)
                                {{ $black_piece }} <span class="white-text">@if(isset($play->cards()->get()->toArray()[$key])) {{ implode('',json_decode($play->cards()->get()->toArray()[$key]['text'])) }} @endif</span>
                            @endforeach
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Nem</button>
                        <a href="{{ route('game.choose.submit', ['game' => $game, 'play' => $play]) }}" class="btn btn-primary">Igen</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endpush
