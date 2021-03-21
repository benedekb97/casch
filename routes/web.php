<?php

Route::get('', 'SiteController@index')->name('index');

Route::get('redirect', 'LoginController@redirect')->name('redirect');
Route::get('callback', 'LoginController@callback')->name('callback');
Route::match(['get','post'],'login', 'LoginController@login')->name('login');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::get('test', 'LoginController@test')->name('test');
Route::match(['get','post'], 'register','LoginController@register')->name('register');

Route::get('activate', 'LoginController@activate')->name('activate')->middleware('auth');
Route::get('activate/{code}', 'LoginController@activateCode')->name('activate.code');
Route::get('activate_resend', 'LoginController@resend')->name('activate.resend')->middleware('auth');

Route::get('help', 'SiteController@help')->name('help');

Route::group([
    'prefix' => 'angus/egy/kuki/mert/szereti/a/nunit',
    'as' => 'angus.'
], static function(){
    Route::get('', 'AngusController@list')->name('index');

    Route::post('response', 'AngusController@setResponse')->name('response');
    Route::post('send', 'AngusController@receive')->name('receive');
    Route::get('empty', 'AngusController@setEmpty')->name('empty');
});

Route::group(['middleware' => ['auth','activate']], function(){
    Route::group(['prefix' => 'cards', 'as' => 'cards.', 'middleware' => 'permissions:edit-cards'], function(){
        Route::get('', 'CardController@index')->name('index');

        Route::get('white', 'CardController@white')->name('white');
        Route::get('black', 'CardController@black')->name('black');

        Route::post('add','CardController@add')->name('add');
        Route::get('delete/{card}', 'CardController@delete')->name('delete');
    });

    Route::match(['get','post'], 'complaint', 'ButtHurtController@butthurt')->name('complaint');

    Route::get('host', 'GameController@host')->name('host')->middleware('permissions:host');
    Route::get('leave/{slug?}', 'GameController@leave')->name('leave');

    Route::group(['prefix' => 'games', 'as' => 'games.'], function(){
        Route::get('{page?}', 'GamesController@index')->name('index');
        Route::get('{game}', 'GamesController@game')->name('game');
    });

    Route::group(['prefix' => 'game', 'as' => 'game.'], function(){
        Route::get('leave', 'GameController@leaveGame')->name('leave');
        Route::post('{slug}/change', 'GameController@change')->name('change');
        Route::post('{slug}/deck', 'GameController@deck')->name('deck');
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

        Route::post('{slug}/votekick', 'GameController@voteKick')->name('vote_kick');

        Route::group(['prefix' => 'chat', 'as' => 'chat.'], function(){
            Route::post('{slug}/send', 'ChatController@send')->name('send');
            Route::post('{slug}', 'ChatController@get')->name('get');
        });
    });

    Route::group(['prefix' => 'plays', 'as' => 'plays.'], function(){
        Route::get('{play}/feature', 'PlayController@feature')->name('feature');
        Route::get('{play}/unfeature', 'PlayController@unfeature')->name('unfeature');
    });

    Route::group([
        'prefix' => 'user',
        'as' => 'user.'
    ], function(){
        Route::get('profile', 'UserController@profile')->name('profile');
        Route::get('decks', 'UserController@decks')->name('decks');

        Route::group([
            'prefix' => 'decks',
            'as' => 'decks.'
        ], function(){
            Route::get('create', 'DeckController@create')->name('create');
            Route::post('save/{deck?}', 'DeckController@save')->name('save');

            Route::get('{deck}/view', 'DeckController@view')->name('view');

            Route::group([
                'prefix' => 'deck',
                'as' => 'deck.'
            ], function(){
                Route::get('{deck}/white/{page?}', 'DeckController@white')->name('white');
                Route::get('{deck}/black/{page?}', 'DeckController@black')->name('black');

                Route::post('{deck}/addBlack/{card?}', 'DeckController@addBlack')->name('addBlack');
                Route::post('{deck}/addWhite/{card?}', 'DeckController@addWhite')->name('addWhite');
                Route::post('{deck}/import', 'DeckController@import')->name('import');

                Route::get('{deck}/card/{card}/remove', 'DeckController@removeCard')->name('deleteCard');
            });
        });

        Route::group([
            'prefix' => 'profile',
            'as' => 'profile.'
        ], function(){
            Route::get('edit','UserController@edit')->name('edit');
            Route::post('save', 'UserController@save')->name('save');
        });
    });

    Route::group([
        'namespace' => 'Admin',
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => 'groups:admin'
    ], function(){
        Route::get('','AdminController@index')->name('index');
        Route::get('base', 'AdminController@createBaseDeck')->name('base');

        Route::group([
            'prefix' => 'games',
            'as' => 'games.',
        ], function(){
            Route::get('{page?}','GameController@index')->name('index');

            Route::get('{game}/view', 'GameController@view')->name('view');
            Route::get('{game}/delete', 'GameController@delete')->name('delete');
            Route::get('{game}/plays/{page?}', 'GameController@plays')->name('plays');
            Route::get('{game}/players', 'GameController@players')->name('players');

            Route::get('end-all', 'GameController@endAll')->name('endAll');
        });

        Route::group([
            'prefix' => 'players',
            'as' => 'players.'
        ], function(){
            Route::get('{player}/view', 'PlayerController@view')->name('view');
            Route::get('{player}/deal', 'PlayerController@deal')->name('deal');
        });
    });

    Route::get('report', 'SiteController@report')->name('report');
    Route::post('report/submit', 'SiteController@submitReport')->name('report.submit');

});


Route::get('disclaimer', 'SiteController@disclaimer')->name('disclaimer');

Route::get('join/{slug}', 'GameController@join')->name('join');

Route::post('upload', 'GameController@upload')->name('upload');

