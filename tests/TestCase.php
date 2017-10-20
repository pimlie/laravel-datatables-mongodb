<?php

namespace Pimlie\DataTables\Tests;

use Pimlie\DataTables\Tests\Models\User;
use Pimlie\DataTables\Tests\Models\Role;
use Pimlie\DataTables\Tests\Models\Post;
use Pimlie\DataTables\Tests\Models\Heart;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->seedDatabase();
    }

    protected function tearDown()
    {
        parent::tearDown();
        
        $this->emptyDatabase();
    }

    protected function emptyDatabase()
    {
        Post::truncate();
        User::truncate();
        Role::truncate();
        Heart::truncate();
    }

    protected function seedDatabase()
    {
        $this->emptyDatabase();

        $adminRole = Role::create(['role' => 'Administrator']);
        $userRole  = Role::create(['role' => 'User']);
        collect(range(1, 20))->each(function ($i) use ($adminRole, $userRole) {
            /** @var User $user */
            $user = User::query()->create([
                'name'  => 'Record-' . $i,
                'email' => 'Email-' . $i . '@example.com',
            ]);
            collect(range(1, 3))->each(function ($i) use ($user) {
                $user->posts()->create([
                    'title' => "User-{$user->id} Post-{$i}",
                ]);
            });
            $user->heart()->create([
                'size' => 'heart-' . $i,
            ]);
            if ($i % 2) {
                $user->roles()->attach(Role::all());
            } else {
                $user->roles()->attach($userRole);
            }
        });
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Jenssegers\Mongodb\MongodbServiceProvider::class,
            \Yajra\DataTables\DataTablesServiceProvider::class,
        ];
    }
    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('database', require('config/database.php'));
        $app['config']->set('datatables', require('config/datatables.php'));
        $app['config']->set('database.default', 'mongodb');
    }
    
    protected function getPackageAliases($app)
    {
        return [
            'DataTables' => \Yajra\DataTables\Facades\DataTables::class,
        ];
    }
}