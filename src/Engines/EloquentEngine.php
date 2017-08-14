<?php

namespace Pimlie\DatatablesMongodb\Engines;

use Yajra\Datatables\Engines\EloquentEngine As YajraEloquentEngine;

/**
 * Class EloquentEngine.
 *
 * @package Yajra\Datatables\Engines
 * @author  Arjay Angeles <aqangeles@gmail.com>
 */
class EloquentEngine extends YajraEloquentEngine
{
    /**
     * Get columns definition.
     *
     * @return array
     */
    protected function getColumnsDefinition()
    {
        $config  = config('datatables-mongodb.columns');
        $allowed = ['excess', 'escape', 'raw', 'blacklist', 'whitelist'];

        return array_merge(array_only($config, $allowed), $this->columnDef);
    }
}