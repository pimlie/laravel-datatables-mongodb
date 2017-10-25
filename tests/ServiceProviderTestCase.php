<?php

namespace Pimlie\DataTables\Tests;

abstract class ServiceProviderTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), 
                            array(\Pimlie\DataTables\MongodbDataTablesServiceProvider::class));
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('datatables', require(__DIR__ . '/../vendor/yajra/laravel-datatables-oracle/src/config/datatables.php'));
    }
}
