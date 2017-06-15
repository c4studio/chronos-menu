<?php

namespace Chronos\Menu\Services;

use Chronos\Content\Models\Content;

class MenuService
{
    public static function reorderItems($items, $depth)
    {
        foreach ($items as $order => $item) {
            $item->depth = $depth;
            $item->order = $order * 10;
            $item->save();

            if ($item->items)
                self::reorderItems($item->items, $depth + 1);
        }
    }

    public static function translateItems($items, $translation, $parent)
    {
        foreach ($items as $item) {
            $translated_item = null;

            if ($item->url_type == 'Content') {
                $content = Content::find($item->url_value);

                if ($content) {
                    // check if translation exists
                    $translated_content = Content::where('language', $translation->language)->where('translation_id', $content->id)->first();
                    if (!$translated_content)
                        $translated_content = Content::where('language', $translation->language)->where('id', $content->translation_id)->first();

                    if ($translated_content) {
                        $translated_item = $item->replicate();
                        $translated_item->menu_id = $translation->id;
                        $translated_item->name = $translated_content->title;
                        $translated_item->original_name = $translated_content->title;
                        $translated_item->parent_id = $parent;
                        $translated_item->url_value = $translated_content->id;
                        $translated_item->push();
                    }
                }
            }
            else {
                $translated_item = $item->replicate();
                $translated_item->menu_id = $translation->id;
                $translated_item->push();
            }

            if ($translated_item && $item->items)
                self::translateItems($item->items, $translation, $translated_item->id);
        }
    }
}