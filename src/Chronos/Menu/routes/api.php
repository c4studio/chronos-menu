<?php

Route::get('menus', ['uses' => 'MenuController@index', 'as' => 'api.menu']);
Route::delete('menus/{menu}', ['uses' => 'MenuController@destroy', 'as' => 'api.menu.destroy']);
Route::get('menus/{menu}', ['uses' => 'MenuController@show', 'as' => 'api.menu.show']);
Route::get('menus/{menu}/translate', ['uses' => 'MenuController@translate', 'as' => 'api.menu.translate']);
Route::post('menus', ['uses' => 'MenuController@store', 'as' => 'api.menu.store']);
Route::patch('menus/{menu}', ['uses' => 'MenuController@update', 'as' => 'api.menu.update']);

Route::delete('menus/item/{menu}/{item}', ['uses' => 'MenuItemController@destroy', 'as' => 'api.menu.item.destroy']);
Route::post('menus/item/{menu}', ['uses' => 'MenuItemController@store', 'as' => 'api.menu.item.store']);
Route::patch('menus/item/{menu}/{item}', ['uses' => 'MenuItemController@update', 'as' => 'api.menu.item.update']);
Route::patch('menus/item/{menu}/{drop_element}/{drag_element}/insert', ['uses' => 'MenuItemController@insert', 'as' => 'api.menu.item.insert']);
Route::patch('menus/item/{menu}/{drop_element}/{drag_element}/indent', ['uses' => 'MenuItemController@indent', 'as' => 'api.menu.item.indent']);