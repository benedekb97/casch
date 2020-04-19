@extends('layouts.main')

@section('title',$deck->name . " - Fekete kártyák")

@section('content')
    <h2 class="page-header with-description">{{ $deck->name }} - Fekete kártyák</h2>
    <h2 class="page-description">
        <a href="{{ route('user.decks.view', ['deck' => $deck]) }}">Vissza a paklihoz</a>
    </h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header">
                    <h5 class="card-title card-top">Pakli</h5>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.decks.view', ['deck' => $deck]) }}">Információk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.decks.deck.white', ['deck' => $deck]) }}">Fehér kártyák</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('user.decks.deck.black', ['deck' => $deck]) }}">Fekete kártyák</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title card-top">Fekete kártyák</h5>
                </div>
                <div class="card-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('user.decks.deck.black', ['deck' => $deck]) }}">Összes kártya</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-toggle="modal" data-target="#new-card">Új kártya</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row justify-content-center" style="margin-bottom:15px;">
                <?php
                if($page == null || $page == 0){
                    $page = 1;
                }
                ?>
                @if($page>1)
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
            <div class="row">
                @foreach($cards as $card)
                    <div class="col-md-4">
                        <div class="card" id="black-card">
                            <div class="card-body">
                                <h4 class="card-title">{{ $card->getTextBlack() }}</h4>
                            </div>
                            <div class="card-footer">
                                @if($card->user_id === Auth::id())
                                    <span data-toggle="tooltip" title="Szerkesztés">
                                        <button class="btn btn-warning btn-sm" type="button" data-toggle="modal" data-target="#edit-{{ $card->id }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </span>
                                @endif
                                <span data-toggle="tooltip" title="Törlés">
                                    <button class="btn btn-danger btn-sm" type="button" data-toggle="modal" data-target="#delete-{{ $card->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row justify-content-center" style="margin-top:15px;">
                <?php
                if($page == null || $page == 0){
                    $page = 1;
                }
                ?>
                @if($page>1)
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => 1]) }}" class="btn btn-sm btn-primary">1</a>&nbsp;
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => $page-1]) }}" class="btn btn-sm btn-primary">&laquo;</a>&nbsp;
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">1</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&laquo;</a>&nbsp;
                @endif
                <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $page }}</a>&nbsp;
                @if($page<$pages)
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => $page + 1]) }}" class="btn btn-sm btn-primary">&raquo;</a>&nbsp;
                    <a href="{{ route('user.decks.deck.black', ['deck' => $deck, 'page' => $pages]) }}" class="btn btn-sm btn-primary">{{ $pages }}</a>
                @else
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">&raquo;</a>&nbsp;
                    <a class="btn btn-sm btn-default" style="cursor:not-allowed !important;">{{ $pages }}</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="new-card">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Új <i>fekete</i> kártya hozzáadása</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('user.decks.deck.addBlack', ['deck' => $deck]) }}" method="POST">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group" style="margin-bottom:-5px;">
                            <p style="margin-bottom:0; margin-top:-7px">Ahol kiegészítendő hely van oda azt írd: &lt;blank&gt;!</p>
                            <p style="margin-bottom:5px; font-style:italic; font-size:10pt;">Ha egy kérdés, akkor is írj a végére!</p>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="text" class="input-group-text">Szöveg</label>
                                </span>
                                <input type="text" name="text" id="text" placeholder="Szöveg" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                            Mégse <i class="fa fa-times"></i>
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            Mentés <i class="fa fa-save"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @foreach($cards as $card)
        <div class="modal fade" id="edit-{{ $card->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Kártya szerkesztése</h4>
                        <button class="close" type="button" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{ route('user.decks.deck.addBlack', ['deck' => $deck, 'card' => $card]) }}" method="POST">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group" style="margin-bottom:-5px;">
                                <p style="margin-bottom:0; margin-top:-7px">Ahol kiegészítendő hely van oda azt írd: &lt;blank&gt;!</p>
                                <p style="margin-bottom:5px; font-style:italic; font-size:10pt;">Ha egy kérdés, akkor is írj a végére!</p>
                                <div class="input-group">
                                <span class="input-group-prepend">
                                    <label for="text" class="input-group-text">Szöveg</label>
                                </span>
                                    <input type="text" name="text" id="text" placeholder="Szöveg" class="form-control" required value="{{ implode('<blank>', json_decode($card->text)) }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                                Mégse <i class="fa fa-times"></i>
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                Mentés <i class="fa fa-save"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete-{{ $card->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Kártya eltávolítása</h4>
                        <button class="close" type="button" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Biztosan törlöd ezt a kártyát?</p>
                        <p>{{ $card->getTextBlack() }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                            Mégse <i class="fa fa-times"></i>
                        </button>
                        <a href="{{ route('user.decks.deck.deleteCard', ['deck' => $deck, 'card' => $card]) }}" class="btn btn-danger btn-sm">
                            Törlés <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endpush
