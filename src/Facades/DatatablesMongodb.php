<?php

namespace Pimlie\DatatablesMongodb\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Datatables.
 *
 * @package Pimlie\DatatablesMongodb\Facades
 */
class DatatablesMongodb extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'datatables-mongodb';
    }
}
