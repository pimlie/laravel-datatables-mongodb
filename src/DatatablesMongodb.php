<?php

namespace Pimlie\DatatablesMongodb;

use Yajra\Datatables\Datatables;

/**
 * Class Datatables.
 *
 * @package Pimlie\Datatables
 */
class DatatablesMongodb extends Datatables
{
    /**
     * Gets query and returns instance of class.
     *
     * @param  mixed $source
     * @return mixed
     * @throws \Exception
     */
    public static function of($source)
    {
        $datatables = app('datatables-mongodb');
        $config     = app('config');
        $engines    = $config->get('datatables-mongodb.engines');
        $builders   = $config->get('datatables-mongodb.builders');

        if (is_array($source)) {
            $source = new Collection($source);
        }

        foreach ($builders as $class => $engine) {
            if ($source instanceof $class) {
                $class = $engines[$engine];

                return new $class($source, $datatables->getRequest());
            }
        }

        throw new \Exception('No available engine for ' . get_class($source));
    }
    
    /**
     * Datatables using Query Builder.
     *
     * @param \Jenssegers\Mongodb\Query\Builder $builder
     * @return \Yajra\Datatables\Engines\QueryBuilderEngine
     */
    public function mongodbQueryBuilder($builder)
    {
        return new Engines\MongodbQueryBuilderEngine($builder, $this->request);
    }

    /**
     * Datatables using Monogodb Eloquent Builder.
     *
     * @param \Jenssegers\Mongodb\Eloquent\Builder $builder
     * @return \Yajra\Datatables\Engines\EloquentEngine
     */
    public function moloquent($builder)
    {
        return new Engines\MoloquentEngine($builder, $this->request);
    }

    /**
     * Datatables using Eloquent Builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder|mixed $builder
     * @return \Yajra\Datatables\Engines\EloquentEngine
     */
    public function eloquent($builder)
    {
        return new Engines\EloquentEngine($builder, $this->request);
    }
}
