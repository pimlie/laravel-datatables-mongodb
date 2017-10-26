<?php

namespace Pimlie\DataTables;

use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\DataTables;

class MongodbDataTablesServiceProvider extends ServiceProvider
{
    static protected $engines = [
        'moloquent'      => MongodbDataTable::class,
        'mongodb-query'  => MongodbQueryDataTable::class,
        'mongodb-hybrid' => HybridMongodbQueryDataTable::class,
    ];

    /**
     * Boot the instance, add macros for datatable engines
     *
     * @return void
     */
    public function boot()
    {
        $engines = config('datatables.engines');
        if (!is_array($engines)) {
            $this->app['config']->set('datatables.engines', static::$engines);
        } else {
            $this->app['config']->set('datatables.engines', array_merge(static::$engines, $engines));
        }
        
        foreach (static::$engines as $engine => $class) {
            $engine = camel_case($engine);

            if (!DataTables::hasMacro($engine)) {
                DataTables::macro($engine, function () use ($class) {
                    if (!call_user_func_array(array($class, 'canCreate'), func_get_args())) {
                        throw new \InvalidArgumentException();
                    }
                    return call_user_func_array(array($class, 'create'), func_get_args());
                });
            }
        }
    }
}
