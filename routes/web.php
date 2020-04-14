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
});

Route::get('join/{slug}', 'GameController@join')->name('join');

Route::group(['prefix' => 'game', 'as' => 'game.'], function(){
    Route::post('{game}/change', 'GameController@change')->name('change');
    Route::post('{game}/start', 'GameController@start')->name('start');
    Route::post('{game}/data', 'GameController@data')->name('data');

    Route::get('{slug}', 'GameController@play')->name('play');
});
