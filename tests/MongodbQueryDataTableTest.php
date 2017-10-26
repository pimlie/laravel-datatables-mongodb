<?php
namespace Pimlie\DataTables\Tests;

use DB;
use Illuminate\Http\JsonResponse;
use Jenssegers\Mongodb\Query\Builder;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables as DatatablesFacade;
use Pimlie\DataTables\MongodbQueryDataTable;
use Pimlie\DataTables\Tests\TestCase;

class MongodbQueryDataTableTest extends TestCase
{
    /** @test */
    public function it_returns_all_records_when_no_parameters_is_passed()
    {
        $crawler = $this->call('GET', '/query/users');
        $crawler->assertJson([
            'draw'            => 0,
            'recordsTotal'    => 20,
            'recordsFiltered' => 20,
        ]);
    }

    /** @test */
    public function it_can_perform_global_search()
    {
        $crawler = $this->call('GET', '/query/users', [
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
    public function it_can_perform_multiple_term_global_search()
    {
        $crawler = $this->call('GET', '/query/users', [
            'columns' => [
                ['data' => 'name', 'name' => 'name', 'searchable' => "true", 'orderable' => "true"],
                ['data' => 'email', 'name' => 'email', 'searchable' => "true", 'orderable' => "true"],
            ],
            'search'  => ['value' => 'Record-19 Email-19'],
        ]);

        $crawler->assertJson([
            'draw'            => 0,
            'recordsTotal'    => 20,
            'recordsFiltered' => 1,
        ]);
    }

    /** @test */
    public function it_accepts_a_query_builder_using_of_factory()
    {
        $dataTable = DataTables::of(DB::table('users'));
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbQueryDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test */
    public function it_accepts_a_query_builder_using_facade()
    {
        $dataTable = DatatablesFacade::of(DB::table('users'));
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbQueryDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test *
    public function it_accepts_a_query_builder_using_facade_query_method()
    {
        $dataTable = DatatablesFacade::mongodbQueryBuilder(DB::table('users'));
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbQueryDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test *
    public function it_accepts_a_query_builder_using_ioc_container()
    {
        $dataTable = app('datatables')->query(DB::table('users'));
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbQueryDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test */
    public function it_accepts_a_query_builder_using_ioc_container_factory()
    {
        $dataTable = app('datatables')->of(DB::table('users'));
        $response  = $dataTable->make(true);
        $this->assertInstanceOf(MongodbQueryDataTable::class, $dataTable);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /** @test */
    public function it_does_not_allow_search_on_added_columns()
    {
        $crawler = $this->call('GET', '/query/addColumn', [
            'columns' => [
                ['data' => 'foo', 'name' => 'foo', 'searchable' => "true", 'orderable' => "true"],
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
    public function it_can_return_auto_index_column()
    {
        $crawler = $this->call('GET', '/query/indexColumn', [
            'columns' => [
                ['data' => 'DT_Row_index', 'name' => 'index', 'searchable' => "false", 'orderable' => "false"],
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

        $this->assertArrayHasKey('DT_Row_Index', $crawler->json()['data'][0]);
    }

    /** @test */
    public function it_allows_search_on_added_column_with_custom_filter_handler()
    {
        $crawler = $this->call('GET', '/query/filterColumn', [
            'columns' => [
                ['data' => 'foo', 'name' => 'foo', 'searchable' => "true", 'orderable' => "true"],
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

        $queries = $crawler->json()['queries'];
        $this->assertContains('{"foo":"Record-19"}', $queries[1]['query']);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->app['router']->get('/query/users', function () {
            return datatables(DB::table('users'))->make('true');
        });

        $this->app['router']->get('/query/addColumn', function () {
            return datatables(DB::table('users'))
                             ->addColumn('foo', 'bar')
                             ->make('true');
        });

        $this->app['router']->get('/query/indexColumn', function () {
            return datatables(DB::table('users'))
                             ->addIndexColumn()
                             ->make('true');
        });

        $this->app['router']->get('/query/filterColumn', function () {
            return datatables(DB::table('users'))
                             ->addColumn('foo', 'bar')
                             ->filterColumn('foo', function (Builder $builder, $keyword) {
                                 $builder->where('foo', $keyword);
                             })
                             ->make('true');
        });
    }
}
