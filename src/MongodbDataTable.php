<?php

namespace Pimlie\DataTables;

use Jenssegers\Mongodb\Eloquent\Builder;

class MongodbDataTable extends MongodbQueryDataTable
{
    /**
     * @var \Jenssegers\Mongodb\Eloquent\Builder
     */
    protected $query;

    /**
     * MongodbDataTable constructor.
     *
     * @param mixed $model
     */
    public function __construct($model)
    {
        $builder = $model instanceof Builder ? $model : $model->getQuery();
        parent::__construct($builder->getQuery());

        $this->query = $builder;
    }

    /**
     * Not supported: Add columns in collection.
     *
     * @param  array  $names
     * @param  bool|int  $order
     * @return $this
     */
    public function addColumns(array $names, $order = false)
    {
        return $this;
    }

    /**
     * If column name could not be resolved then use primary key.
     *
     * @return string
     */
    protected function getPrimaryKeyName()
    {
        return $this->query->getModel()->getKeyName();
    }
}
