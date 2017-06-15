# Chronos CMS

---

Companion menu builder package to Chronos CMS

---

## Installation

It's as easy as:

    composer require c4studio/chronos-menu

After composer has run add following lines to the providers[] array in ```app/config/app.php```:

```php
...
Chronos\Menu\MenuServiceProvider::class,
...
```

### Publish assets

Next we need to publish all the assets belonging to Chronos Menu:

	php artisan vendor:publish --tag=public

Note: if you wish to only publish Chronos Menu assets, you might want to use the --provider flag.

### Run migrations

Almost done. We need to run the migrations and seed our database:

```
php artisan migrate
php artisan db:seed --class=\\Chronos\\Menu\\Seeds\\DatabaseSeeder
```

### Route maps

Chronos uses the RouteMap facade to connect Content models present in the menu with their respective controller actions (and URLs).

You can define a new route mapping in any route file, like web.php, just like you would do for routes.

You can define a route mapping for all Content models of a specific content type, or individual models.

```
RouteMap::add('PagesController@show', 'Page');		// Route mapping for all pages
RouteMap::add('PagesController@about', 'Page', 13);	// Route mapping for page with ID of 13
```

Note: individual route mapings always take precedence

---
[http://c4studio.ro](http://c4studio.ro)

P.S.: You're awesome for being on this page