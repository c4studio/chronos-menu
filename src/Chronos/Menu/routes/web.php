<?php

Route::group(['middleware' => 'auth'], function () {
    Route::get('menus', ['uses' => 'MenuController@index', 'as' => 'chronos.menu']);
    Route::get('menus/{menu}/edit', ['uses' => 'MenuController@edit', 'as' => 'chronos.menu.edit']);
});