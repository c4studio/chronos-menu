<?php

namespace Chronos\Menu\Api\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Menu\Models\Menu;
use Chronos\Menu\Models\MenuItem;
use Chronos\Menu\Services\MenuService;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function destroy(Menu $menu, MenuItem $item)
    {
        if ($item->delete()) {
            MenuService::reorderItems($menu->items, 0);

            return response(null, 200);
        }
        else {
            return response(null, 500);
        }
    }

    public function indent(Menu $menu, MenuItem $drop_element, MenuItem $drag_element)
    {
        $drag_element->parent_id = $drop_element->id;
        $drag_element->order = MenuItem::where('parent_id', $drop_element->id)->count();
        $drag_element->save();

        MenuService::reorderItems($menu->items, 0);

        return response(null, 200);
    }

    public function insert(Menu $menu, MenuItem $drop_element, MenuItem $drag_element)
    {
        $drag_element->parent_id = $drop_element->parent_id;
        $drag_element->order = $drop_element->order + 1;
        $drag_element->save();

        MenuService::reorderItems($menu->items, 0);

        return response(null, 200);
    }

    public function store(Request $request, Menu $menu)
    {
        // validate input
        $this->validate($request, [
            'name' => 'required',
            'url_value' => $request->get('url_type') == 'URL' ? 'required|url' : 'required'
        ], [
            'name.required' => trans('validation.required', ['attribute' => trans('chronos.menu::forms.Link text')]),
            'url_value.required' => trans('validation.required', ['attribute' => trans('chronos.menu::forms.URL')]),
            'url_value.url' => trans('validation.url', ['attribute' => trans('chronos.menu::forms.URL')])
        ]);

        // create menu item
        $item = MenuItem::create([
            'depth' => 0,
            'menu_id' => $menu->id,
            'name' => $request->get('name'),
            'order' => MenuItem::where('menu_id', $menu->id)->where('depth', 0)->count() * 10,
            'original_name' => $request->get('name'),
            'parent_id' => null,
            'status' => 1,
            'url_anchor' => '',
            'url_target' => '',
            'url_type' => $request->get('url_type'),
            'url_value' => $request->get('url_value')
        ]);

        return response()->json([
            'item' => $item,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, Menu $menu, MenuItem $item)
    {
        // validate input
        $this->validate($request, [
            'name' => 'required'
        ], [
            'name.required' => trans('validation.required', ['attribute' => trans('chronos.menu::forms.Navigation item label')]),
        ]);

        // update menu
        $item->name = $request->get('name');
        $item->status = $request->get('status');
        $item->url_anchor = $request->get('url_anchor');
        $item->url_target = $request->get('url_target');
        $item->save();

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.menu::alerts.Success.'),
                    'message' => trans('chronos.menu::alerts.Menu item successfully updated.'),
                ]
            ],
            'item' => $item,
            'status' => 200
        ], 200);
    }

}