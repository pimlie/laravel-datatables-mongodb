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

## Usage

### Use the dataTable class directly.

```php
use Pimlie\DataTables\MongodbDataTable;

$model = new App\User;

return (new MongodbDataTable($model))->toJson()
```

### Use via trait.
1. You need to use `MongodbDataTable` trait on your model.

```php
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
- there is no support for viewing/searching/ordering on (non-embedded) relationships between Models through a `user.posts` column key,


