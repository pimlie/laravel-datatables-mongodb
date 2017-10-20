# Laravel DataTables Mongodb Plugin

[![Latest Stable Version](https://img.shields.io/packagist/v/pimlie/laravel-datatables-mongodb.svg)](https://packagist.org/packages/pimlie/laravel-datatables-mongodb)
[![Total Downloads](https://img.shields.io/packagist/dt/pimlie/laravel-datatables-mongodb.svg)](https://packagist.org/packages/pimlie/laravel-datatables-mongodb)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/pimlie/laravel-datatables-mongodb)

This package is a plugin for [Laravel DataTables](https://github.com/yajra/laravel-datatables) to support Mongodb using [Laravel Mongodb](https://github.com/jenssegers/laravel-mongodb/)

## Requirements
- [Laravel DataTables >=8.0](https://github.com/yajra/laravel-datatables)
- [Laravel Mongodb](https://github.com/jenssegers/laravel-mongodb)

## Documentation
- [Laravel DataTables Documentation](http://yajrabox.com/docs/laravel-datatables)

This plugin provides most functionalities described in the Laravel Datatables documentation. See `Known issues` below

## Installation
`composer require pimlie/laravel-datatables-mongodb:^1.0`

## Configure

Check the Laravel DataTables configuration for how to configure and use it.

If you want to use the `datables()` method to automatically use the correct datatables engine, open the `config/datatables.php` file and add the following:

```php
    /**
     * Datatables list of available engines.
     * This is where you can register your custom datatables engine.
     */
    'engines'        => [
        'eloquent'   => Yajra\DataTables\EloquentDataTable::class,
        'query'      => Yajra\DataTables\QueryDataTable::class,
        'collection' => Yajra\DataTables\CollectionDataTable::class,
        
        'moloquent'    => Pimlie\DataTables\MongodbDataTable::class,
        'mongodb-query' => Pimlie\DataTables\MongodbQueryDataTable::class,
    ],

    /**
     * Datatables accepted builder to engine mapping.
     */
    'builders'       => [
        // The Jenssegers\Mongodb classes extend the default Eloquent classes
        // and need to be listed above them in this list!
        Jenssegers\Mongodb\Eloquent\Builder::class             => 'moloquent',
        Jenssegers\Mongodb\Query\Builder::class                => 'mongodb-query',
        // This is the Builder class used for HybridRelations,
        // you can remove it if you dont use HybridRelations
        Jenssegers\Mongodb\Helpers\EloquentBuilder::class      => 'eloquent',

        Illuminate\Database\Eloquent\Relations\Relation::class => 'eloquent',
        Illuminate\Database\Eloquent\Builder::class            => 'eloquent',
        Illuminate\Database\Query\Builder::class               => 'query',
        Illuminate\Support\Collection::class                   => 'collection',
    ],
```

## Usage

### Use the `datatables()` method

For this to work you need to have the class definitions added to the `engines` and `builders` datatables configuration, see above.

```php
use \App\MyMongodbModel;

$model = new MyMongodbModel();

$datatables = datatables($model);

```

### Use the dataTable class directly.

```php
use Pimlie\DataTables\MongodbDataTable;

$model = new App\User;

return (new MongodbDataTable($model))->toJson()
```

### Use via trait.
1. You need to use `MongodbDataTable` trait on your model.

```php
use Jenssegers\Mongodb\Eloquent\Model;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class User extends Model
{
	use MongodbDataTableTrait;
}
```

2. Process dataTable directly from your model.

```php
Route::get('users/data', function() {
	return User::dataTable()->toJson();
});
```

## Known issues

- the `orderColumn` and `orderColumns` methods are _not_ available
- wildcard search is not supported yet
- there is no support for viewing/searching/ordering on (non-embedded) relationships between Models through a `user.posts` column key,


