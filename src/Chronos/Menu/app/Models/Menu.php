<?php

namespace Chronos\Menu\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['admin_urls', 'endpoints', 'translation_codes'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'language', 'translation_id', 'lock_delete'];



    /**
     * Add admin URLs to model.
     */
    public function getAdminUrlsAttribute()
    {
        $id = $this->attributes['id'];

        $urls['index'] = route('chronos.menu');
        $urls['edit'] = route('chronos.menu.edit', ['menu' => $id]);

        if (settings('is_multilanguage')) {
            foreach ($this->translations as $translation) {
                $urls['translations'][$translation->language] = route('chronos.menu.edit', ['menu' => $translation->id]);
            }
        }

        return $urls;
    }

    /**
     * Add endpoints to model.
     */
    public function getEndpointsAttribute()
    {
        $id = $this->attributes['id'];

        $endpoints['index'] = route('api.menu');
        $endpoints['destroy'] = route('api.menu.destroy', ['menu' => $id]);

        if (settings('is_multilanguage'))
            $endpoints['translate'] = route('api.menu.translate', ['menu' => $id]);

        return $endpoints;
    }

    /**
     * Add language to model.
     */
    public function getLanguageNameAttribute()
    {
        return array_search($this->language, array_column(\Config::get('languages.list'), 'code', 'name'));
    }

    /**
     * Get content translations.
     */
    public function getTranslationsAttribute()
    {
        if (!settings('is_multilanguage'))
            return null;

        $parent = $this->translation_id !== null ? Menu::where('id', $this->translation_id)->first() : Menu::where('id', $this->id)->first();
        $translations = Menu::where('translation_id', $parent->id)->get();

        return $translations->merge([$parent]);
    }

    /**
     * Get content translations.
     */
    public function getTranslationCodesAttribute()
    {
        $translations = $this->translations;

        if (!$translations)
            return [];

        return $translations->map(function($content) {
            return $content->language;
        });
    }



    /**
     * Get menu items.
     */
    public function items()
    {
        return $this->hasMany('\Chronos\Menu\Models\MenuItem')->where('parent_id', null);
    }
}
