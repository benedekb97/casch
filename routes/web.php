<?php

Route::get('', 'SiteController@index')->name('index');

Route::get('login', 'LoginController@redirect')->name('login');
Route::get('callback', 'LoginController@callback')->name('callback');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::get('test', 'LoginController@test')->name('test');

Route::group(['middleware' => 'auth'], function(){
    Route::group(['prefix' => 'cards', 'as' => 'cards.', 'middleware' => 'permissions:edit-cards'], function(){
        Route::get('', 'CardController@index')->name('index');

        Route::get('white', 'CardController@white')->name('white');
        Route::get('black', 'CardController@black')->name('black');

        Route::post('add','CardController@add')->name('add');
        Route::get('delete/{card}', 'CardController@delete')->name('delete');
    });

    Route::get('host', 'GameController@host')->name('host')->middleware('permissions:host');
    Route::get('leave/{slug?}', 'GameController@leave')->name('leave');

    Route::group(['prefix' => 'games', 'as' => 'games.'], function(){
        Route::get('', 'GamesController@index')->name('index');
        Route::get('{game}', 'GamesController@game')->name('game');
    });

    Route::group(['prefix' => 'game', 'as' => 'game.'], function(){
        Route::post('{slug}/change', 'GameController@change')->name('change');
        Route::post('{game}/start', 'GameController@start')->name('start');
        Route::post('{game}/data', 'GameController@data')->name('data');
        Route::post('{game}/submit', 'GameController@submit')->name('submit');

        Route::get('{game}/choose-turn-winner', 'GameController@choose')->name('choose_turn_winner');
        Route::get('{game}/choose/{play}/submit', 'GameController@chooseSubmit')->name('choose.submit');

        Route::get('{game}/turn-recap', 'GameController@turnRecap')->name('turn.recap');

        Route::post('{game}/ready', 'GameController@ready')->name('ready');
        Route::post('{game}/like', 'GameController@like')->name('like');

        Route::get('{slug}/finished/{page?}', 'GameController@finished')->name('finished');

        Route::get('{slug}', 'GameController@play')->name('play');

        Route::post('{slug}/lobby/ready', 'GameController@readyLobbyToggle')->name('lobby.ready');
    });

});

Route::get('join/{slug}', 'GameController@join')->name('join');

Route::post('upload', 'GameController@upload')->name('upload');

