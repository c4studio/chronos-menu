<?php

namespace Chronos\Menu\Api\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Menu\Models\Menu;
use Chronos\Menu\Models\MenuItem;
use Chronos\Menu\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{

    public function index()
    {
        return response()->json(Menu::all(), 200);
    }

    public function destroy(Menu $menu)
    {
        if ($menu->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.menu::alerts.Success.'),
                        'message' => trans('chronos.menu::alerts.Menu successfully deleted.'),
                    ]
                ],
                'redirect' => $menu->admin_urls['index'],
                'status' => 200
            ], 200);
        else
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'error',
                        'title' => trans('chronos.menu::alerts.Error.'),
                        'message' => trans('chronos.menu::alerts.Menu deletion was unsuccessful.'),
                    ]
                ],
                'status' => 500
            ], 500);
    }

    public function show(Request $request, Menu $menu)
    {
        if ($request->has('load') && $request->get('load') == 'items')
            $menu->load('items');

        return response()->json($menu, 200);
    }

    public function store(Request $request)
    {
        // validate input
        $this->validate($request, [
            'name' => 'required|unique:menus'
        ]);

        // create menu
        $menu = Menu::create([
            'language' => $request->has('language') ? $request->get('language') : config('app.locale'),
            'name' => $request->get('name')
        ]);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.menu::alerts.Success.'),
                    'message' => trans('chronos.menu::alerts.Menu successfully created.'),
                ]
            ],
            'menu' => $menu,
            'redirect' => $menu->admin_urls['edit'],
            'status' => 200
        ], 200);
    }

    public function translate(Request $request, Menu $menu)
    {
        // duplicate menu
        $translation = $menu->replicate();
        $translation->language = $request->get('language');
        $translation->translation_id = $menu->translation_id == null ? $menu->id : $menu->translation_id;
        $translation->push();

        $items = MenuItem::where('menu_id', $menu->id)->where('parent_id', null)->get();
        MenuService::translateItems($items, $translation, null);

        return redirect($translation->admin_urls['edit']);
    }

    public function update(Request $request, Menu $menu)
    {
        // validate input
        $this->validate($request, [
            'name' => ['required', Rule::unique('menus')->ignore($menu->id)],
            'items.*.name' => 'required'
        ], [
            'items.*.name.required' => trans('chronos.menu::alerts.The navigation item label field is required.')
        ]);

        // update menu
        $menu->name = $request->get('name');
        $menu->save();

        // update menu items
        if ($request->has('items')) {
            MenuItem::where('menu_id', $menu->id)->delete();

            foreach ($request->get('items') as $item) {
                MenuItem::create([
                    'depth' => 0,
                    'menu_id' => $menu->id,
                    'name' => $item['name'],
                    'order' => $item['order'],
                    'original_name' => $item['original_name'],
                    'parent_id' => null,
                    'status' => $item['status'] == 'on',
                    'url_anchor' => ltrim($item['url_anchor'], '#'),
                    'url_target' => $item['url_target'],
                    'url_type' => $item['url_type'],
                    'url_value' => $item['url_value']
                ]);
            }
        }

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.menu::alerts.Success.'),
                    'message' => trans('chronos.menu::alerts.Menu successfully updated.'),
                ]
            ],
            'menu' => $menu,
            'status' => 200
        ], 200);
    }

}