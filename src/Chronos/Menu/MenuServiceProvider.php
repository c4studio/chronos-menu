<?php

namespace Chronos\Menu;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // require routes
        if (!$this->app->routesAreCached()) {
            $this->app['router']->group(['middleware' => 'web', 'namespace' => 'Chronos\Menu\Http\Controllers', 'prefix' => 'admin'], function () {
                require __DIR__ . '/routes/web.php';
            });
            $this->app['router']->group(['middleware' => App::environment('local', 'staging') ? 'api' : ['auth:api', 'bindings'], 'namespace' => 'Chronos\Menu\Api\Controllers', 'prefix' => 'api'], function () {
                require __DIR__ . '/routes/api.php';
            });
        }

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // load translations
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'chronos.menu');

        // load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'chronos');

        // publish views so they can be overridden
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/chronos'),
        ], 'views');

        // publish assets
        $this->publishes([
            __DIR__ . '/assets/' => public_path('chronos'),
        ], 'public');

        // register menu
        $this->updateMenu();
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // default package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/config/defaults.php', 'menu'
        );
    }



    /**
     * Updates menu.
     */
    protected function updateMenu()
    {
        $menu = \Menu::get('ChronosMenu');

        // Menus tab
        $menus_menu = $menu->add(trans('chronos.menu::menu.Menus'), ['route' => 'chronos.menu'])
            ->prepend('<span class="icon c4icon-grid-2"></span>')
            ->data('order', 120)->data('permissions', ['manage_menus']);
    }

}