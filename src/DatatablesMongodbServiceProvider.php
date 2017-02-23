<?php

namespace Pimlie\DatatablesMongodb;

use Yajra\Datatables\DatatablesServiceProvider;
use Yajra\Datatables\Request;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

/**
 * Class DatatablesServiceProvider.
 *
 * @package Pimlie\Datatables
 */
class DatatablesMongodbServiceProvider extends DatatablesServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datatables-mongodb.php', 'datatables-mongodb');
        
        $this->publishes([
            __DIR__ . '/../config/datatables-mongodb.php' => config_path('datatables-mongodb.php'),
        ], 'datatables-mongodb');
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->isLumen()) {
            require_once 'fallback.php';
        }
        $this->app->singleton('datatables-mongodb.fractal', function () {
            $fractal = new Manager;
            $config  = $this->app['config'];
            $request = $this->app['request'];
            $includesKey = $config->get('datatables-mongodb.fractal.includes', 'include');
            if ($request->get($includesKey)) {
                $fractal->parseIncludes($request->get($includesKey));
            }
            $serializer = $config->get('datatables-mongodb.fractal.serializer', DataArraySerializer::class);
            $fractal->setSerializer(new $serializer);
            return $fractal;
        });
        $this->app->alias('datatables-mongodb', DatatablesMongodb::class);
        $this->app->singleton('datatables-mongodb', function () {
            return new DatatablesMongodb(new Request(app('request')));
        });
        $this->registerAliases();
    }
    
    /**
     * Create aliases for the dependency.
     */
    protected function registerAliases()
    {
        if (class_exists('Illuminate\Foundation\AliasLoader')) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('DatatablesMongodb', \Pimlie\DatatablesMongodb\Facades\DatatablesMongodb::class);
        }
    }
    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['datatables-mongodb'];
    }}
