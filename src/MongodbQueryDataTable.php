<?php

namespace Pimlie\DataTables;

use Jenssegers\Mongodb\Eloquent\Builder as MoloquentBuilder;
use Jenssegers\Mongodb\Query\Builder;
use Illuminate\Support\Str;
use Yajra\DataTables\QueryDataTable;
use Yajra\DataTables\Utilities\Helper;

class MongodbQueryDataTable extends QueryDataTable
{
    /**
     * Can the DataTable engine be created with these parameters.
     *
     * @param mixed $source
     * @return boolean
     */
    public static function canCreate($source)
    {
        return $source instanceof Builder;
    }

    /**
     * @param \Jenssegers\Mongodb\Query\Builder $builder
     */
    public function __construct(Builder $builder)
    {
        parent::__construct($builder);
    }

    public function count()
    {
        $builder = clone $this->query;

        return $builder->count();
    }

    protected function wrap($column)
    {
        return $column;
    }

    protected function applyFilterColumn($query, $columnName, $keyword, $boolean = 'and')
    {
        $query    = $this->getBaseQueryBuilder($query);
        $callback = $this->columnDef['filter'][$columnName]['method'];

        if ($this->query instanceof MoloquentBuilder) {
            $builder = $this->query->newModelInstance()->newQuery();
        } else {
            $builder = $this->query->newQuery();
        }

        $callback($builder, $keyword);

        $query->addNestedWhereQuery($this->getBaseQueryBuilder($builder), $boolean);
    }

    protected function getBaseQueryBuilder($instance = null)
    {
        if (!$instance) {
            $instance = $this->query;
        }

        if ($instance instanceof MoloquentBuilder) {
            return $instance->getQuery();
        }

        return $instance;
    }

    protected function compileColumnSearch($i, $column, $keyword)
    {
        if ($this->request->isRegex($i)) {
            $this->regexColumnSearch($column, $keyword);
        } else {
            $this->compileQuerySearch($this->query, $column, $keyword, '');
        }
    }

    protected function regexColumnSearch($column, $keyword)
    {
        $this->query->where($column, 'regex', '/' . $keyword . '/' . ($this->config->isCaseInsensitive() ? 'i' : ''));
    }

    protected function castColumn($column)
    {
        return $column;
    }

    protected function compileQuerySearch($query, $column, $keyword, $boolean = 'or')
    {
        $column = $this->castColumn($column);
        $value  = $this->prepareKeyword($keyword);

        if ($this->config->isCaseInsensitive()) {
            $value .= 'i';
        }

        $query->{$boolean . 'Where'}($column, 'regex', $value);
    }

    protected function addTablePrefix($query, $column)
    {
        return $this->wrap($column);
    }

    protected function prepareKeyword($keyword)
    {
        if ($this->config->isWildcard()) {
            $keyword = Helper::wildcardString($keyword, '.*', $this->config->isCaseInsensitive());
        } elseif ($this->config->isCaseInsensitive()) {
            $keyword = Str::lower($keyword);
        }

        if ($this->config->isSmartSearch()) {
            $keyword = "/.*".$keyword.".*/";
        } else {
            $keyword = "/^".$keyword."$/";
        }

        return $keyword;
    }

    /**
     * Not supported
     * Order each given columns versus the given custom sql.
     *
     * @param array  $columns
     * @param string $sql
     * @param array  $bindings
     * @return $this
     */
    public function orderColumns(array $columns, $sql, $bindings = [])
    {
        return $this;
    }

    /**
     * Not supported
     * Override default column ordering.
     *
     * @param string $column
     * @param string $sql
     * @param array  $bindings
     * @return $this
     * @internal string $1 Special variable that returns the requested order direction of the column.
     */
    public function orderColumn($column, $sql, $bindings = [])
    {
        return $this;
    }

    /**
     * Not supported: https://stackoverflow.com/questions/19248806/sort-by-date-with-null-first
     * Set datatables to do ordering with NULLS LAST option.
     *
     * @return $this
     */
    public function orderByNullsLast()
    {
        return $this;
    }

    public function paging()
    {
        $limit = (int) ($this->request->input('length') > 0 ? $this->request->input('length') : 10);
        if (is_callable($this->limitCallback)) {
            $this->query->limit($limit);
            call_user_func_array($this->limitCallback, [$this->query]);
        } else {
            $start = (int)$this->request->input('start');
            $this->query->skip($start)->take($limit);
        }
    }

    protected function defaultOrdering()
    {
        collect($this->request->orderableColumns())
            ->map(function ($orderable) {
                $orderable['name'] = $this->getColumnName($orderable['column'], true);

                return $orderable;
            })
            ->reject(function ($orderable) {
                return $this->isBlacklisted($orderable['name']) && !$this->hasOrderColumn($orderable['name']);
            })
            ->each(function ($orderable) {
                $column = $this->resolveRelationColumn($orderable['name']);

                if ($this->hasOrderColumn($column)) {
                    $this->applyOrderColumn($column, $orderable);
                } else {
                    $this->query->orderBy($column, $orderable['direction']);
                }
            });
    }

    protected function applyOrderColumn($column, $orderable)
    {
        $this->query->orderBy($column, $orderable['direction']);
    }
}
