<?php

namespace Chronos\Menu\Http\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\Language;
use Chronos\Menu\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{

    public function index()
    {
        if (!Auth::user()->hasPermission('manage_menus')) {
            abort(403);
        }

        return view('chronos::menu.index')->with([
            'menus' => Menu::all()
        ]);
    }

    public function edit(Menu $menu)
    {
        if (!Auth::user()->hasPermission('manage_menus')) {
            abort(403);
        }

        return view('chronos::menu.edit')->with([
            'languages' => Language::active()->get(),
            'menu' => $menu,
            'menus' => Menu::all()
        ]);
    }

}