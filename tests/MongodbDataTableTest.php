<?php

namespace Pimle\DataTables\Tests;

use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables as DatatablesFacade;
use Pimlie\DataTables\MongodbDataTable;
use Pimlie\DataTables\Tests\TestCase;
use Pimlie\DataTables\Tests\Models\User;
use Pimlie\DataTables\Tests\Models\Role;

class MongodbDataTableTest extends TestCase
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
    /** @test *
    public function it_accepts_a_model_using_facade_moloquent_method()
    {
        $dataTable = DatatablesFacade::moloquent(User::query());
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
    /** @test *
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

    protected function setUp()
    {
        parent::setUp();

        $this->app['router']->get('/moloquent/users', function () {
            return datatables(User::query())->make('true');
        });
    }
}