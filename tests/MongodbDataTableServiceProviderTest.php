<?php

namespace Pimlie\DataTables\Tests;

use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables as DatatablesFacade;
use Pimlie\DataTables\MongodbDataTable;
use Pimlie\DataTables\Tests\ServiceProviderTestCase;
use Pimlie\DataTables\Tests\Models\User;
use Pimlie\DataTables\Tests\Models\Role;

class MongodbDataTableTestServiceProvider extends ServiceProviderTestCase
{
    /** @test */
    public function it_returns_all_records_when_no_parameters_is_passed()
    {
        $crawler = $this->call('GET', '/moloquent/users');
        $crawler->assertJson([
            'draw'            => 0,
            'recordsTotal'    => 20,
            'recordsFiltered' => 20,
        ]);
    }

    /** @test */
    public function it_can_perform_global_search()
    {
        $crawler = $this->call('GET', '/moloquent/users', [
            'columns' => [
                ['data' => 'name', 'name' => 'name', 'searchable' => "true", 'orderable' => "true"],
                ['data' => 'email', 'name' => 'email', 'searchable' => "true", 'orderable' => "true"],
            ],
            'search'  => ['value' => 'Record-19'],
        ]);
        $crawler->assertJson([
            'draw'            => 0,
            'recordsTotal'    => 20,
            'recordsFiltered' => 1,
        ]);
    }

    /** @test */
    public function it_returns_all_records_when_using_trait()
    {
        $crawler = $this->call('GET', '/moloquent/roles');
        $crawler->assertJson([
            'draw'            => 0,
            'recordsTotal'    => 2,
            'recordsFiltered' => 2,
        ]);
    }

    /** @test */
    public function it_accepts_a_model_using_of_factory()
    {
        $dataTable = DataTables::of(User::query());
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
    /** @test */
    public function it_accepts_a_model_using_facade()
    {
        $dataTable = DatatablesFacade::of(User::query());
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
    /** @test */
    public function it_accepts_a_model_using_facade_moloquent_method()
    {
        $dataTable = DatatablesFacade::moloquent(User::query());
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
    /** @test */
    public function it_accepts_a_model_using_ioc_container()
    {
        $dataTable = app('datatables')->moloquent(User::query());
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
    /** @test */
    public function it_accepts_a_model_using_ioc_container_factory()
    {
        $dataTable = app('datatables')->of(User::query());
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test */
    public function it_accepts_a_model_using_trait()
    {
        $dataTable = Role::dataTable();
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test */
    public function it_accepts_a_relation()
    {
        $dataTable = Datatables::of((new User)->roles());
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test */
    public function it_returns_all_records_when_using_relation()
    {
        $crawler = $this->call('GET', '/moloquent/userRoles');
        $crawler->assertJson([
            'draw'            => 0,
            'recordsTotal'    => 2,
            'recordsFiltered' => 2,
        ]);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->app['router']->get('/moloquent/users', function () {
            return datatables(User::query())->make('true');
        });

        $this->app['router']->get('/moloquent/userRoles', function () {
            return datatables(User::first()->roles())->make('true');
        });

        $this->app['router']->get('/moloquent/roles', function () {
            return Role::dataTable()->make('true');
        });
    }
}
