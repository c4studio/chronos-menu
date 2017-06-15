<?php

namespace Chronos\Menu\Models;

use Chronos\Content\Models\Content;
use Chronos\Content\Models\ContentType;
use Chronos\Scaffolding\App\Facades\RouteMap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['menu_id', 'name', 'original_name', 'url_type', 'url_value', 'url_anchor', 'url_target', 'data', 'parent_id', 'depth', 'order', 'status'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['items'];



    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order', 'ASC');
        });
    }


    /**
     * Get URL for menu item
     */
    public function getUrlAttribute()
    {
        $url_type = $this->attributes['url_type'];
            
        if ($url_type == 'URL') {
            return $this->attributes['url_value'];
        }
        else {
            $content = Content::where('id', $this->attributes['url_value'])->first();
            $type = ContentType::where('id', $content->type_id)->first()->name;

            return RouteMap::get($type, $content->id);
        }
    }



    /**
     * Get descendant items.
     */
    public function items()
    {
        return $this->hasMany('\Chronos\Menu\Models\MenuItem', 'parent_id');
    }

}
