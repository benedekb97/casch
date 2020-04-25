@extends('layouts.main')

@section('title','CaSCH tutoriál')

@section('content')
    <div class="row">
        <div class="col-md-4">
            @auth
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title card-top">Segítség</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#start" class="nav-link">Játék létrehozása</a>
                            </li>
                            <li class="nav-item">
                                <a href="#invite" class="nav-link">Játékosok meghívása</a>
                            </li>
                            <li class="nav-item">
                                <a href="#rounds" class="nav-link">Körök száma</a>
                            </li>
                            <li class="nav-item">
                                <a href="#deck" class="nav-link">Pakli</a>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title card-top">Segítség</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="#register">Regisztráció</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#login">Bejelentkezés</a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endauth
        </div>
        <div class="col-md-8">
            @auth
                <div class="card" style="margin-bottom:15px;">
                    <div class="card-header" id="start">
                        <h4 class="card-title card-title-no-margin">Játék létrehozása:</h4>
                    </div>
                    <div class="card-body">
                        <p style="padding-left:10px; margin-bottom:-10px;">
                            A főoldalon kattints az "Új játék" gombra
                            <img style="width:100%; margin-top:5px;" src="{{ asset('img/help/new_game.png') }}" alt="new_game">
                        </p>
                    </div>
                    <div class="card-header" id="invite">
                        <h5 class="card-title card-title-no-margin">Játékosok meghívása:</h5>
                    </div>
                    <div class="card-body">
                        <p style="padding-left:10px; margin-bottom:-10px;">
                            Miután bedobott az új játékszobába, meg tudsz hívni további játékosokat. Ha belekattintasz a bekeretezett dobozba, kimásolja automatikusan a meghívó linket, amit ha elküldesz valakinek be fog tudni lépni vele az általad létrehozott játékba.
                            <img style="width:100%; margin-top:5px" src="{{ asset('img/help/invite.png') }}" alt="invite">
                        </p>
                    </div>
                    <div class="card-header" id="rounds">
                        <h5 class="card-title card-title-no-margin">Körök száma: </h5>
                    </div>
                    <div class="card-body">
                        <p style="padding-left:10px; margin-bottom:-10px;">
                            Ezzel az opcióval tudod beállítani a körök számát<br>
                            Egy kör mindig annyi játszmából áll ahányan a játékban vagytok.<br>
                            <i>Tipp: Ha sokan vagytok 2-3-nál ne legyen több kör, mert hamar unalmassá válhat úgy a játék.</i>
                        </p>
                    </div>
                    <div class="card-header" id="deck">
                        <h5 class="card-title card-title-no-margin">Pakli: </h5>
                    </div>
                    <div class="card-body">
                        <p style="padding-left:10px; margin-bottom:-10px;">
                            Ezzel az opcióval tudod kiválasztani azt a paklit amiből a játék során kaptok kártyákat. Paklit a profilodban tudsz létrehozni, össze tudod fésülni más emberek pakliaival, illetve tudsz hozzáadni új kártyákat a paklidhoz.
                        </p>
                    </div>
                </div>
            @else
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header" id="register">
                    <h4 class="card-title card-title-no-margin">Regisztráció</h4>
                </div>
                <div class="card-body">
                    <p>Ha még nincs felhasználód az oldalon két opciód van:</p>
                    <ul>
                        <li>Ha rendelkezel <span style="cursor:help" data-toggle="tooltip" title="Ha BME-s vagy be tudsz jelentkezni Címtár azonosítóval, ellenkező esetben a másik opció áll rendelkezésre"><a href="http://auth.sch.bme.hu">AuthSCH</a><sup>?</sup></span> felhasználóval jelentkezz be vele itt: <a href="{{ route('redirect') }}">AuthSCH bejelentkezés</a></li>
                        <li>Ha nem, akkor regisztrálj felhasználót itt: <a href="{{ route('register') }}">Regisztráció</a></li>
                        <li>Regisztráció után aktiválnod kell majd felhasználódat, amit az emailben kapott linken tehetsz meg.</li>
                    </ul>
                </div>
            </div>
            <div class="card" style="margin-bottom:15px;">
                <div class="card-header" id="login">
                    <h4 class="card-title card-title-no-margin">Bejelentkezés</h4>
                </div>
                <div class="card-body">
                    <p>Ha rendelkzel <span style="cursor:help" data-toggle="tooltip" title="Ha BME-s vagy be tudsz jelentkezni Címtár azonosítóval, ellenkező esetben a másik opció áll rendelkezésre"><a href="http://auth.sch.bme.hu">AuthSCH</a><sup>?</sup></span> felhasználóval jelentkezz be vele itt: <a href="{{ route('redirect') }}">AuthSCH bejelentkezés</a></p>
                    <p>Ellenkező esetben regisztráció után be tudsz jelentkezni itt: <a href="{{ route('login') }}">Bejelentkezés</a></p>
                </div>
            </div>
            @endauth
        </div>
    </div>
@endsection
