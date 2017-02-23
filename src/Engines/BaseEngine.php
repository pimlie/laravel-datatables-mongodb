<?php
namespace Pimlie\DatatablesMongodb\Engines;

use Yajra\Datatables\Engines\BaseEngine As YajraBaseEngine;
use Pimlie\DatatablesMongodb\Processors\DataProcessor;


class BaseEngine extends YajraBaseEngine
{
    /**
     * Get processed data
     *
     * @param bool|false $object
     * @return array
     */
    protected function getProcessedData($object = false)
    {
        $processor = new DataProcessor(
            $this->results(),
            $this->columnDef,
            $this->templates,
            $this->request->input('start')
        );
        return $processor->process($object);
    }
    
    /**
     * Get config is case insensitive status.
     *
     * @return bool
     */
    public function isCaseInsensitive()
    {
        return !! config('datatables-mongodb.search.case_insensitive', false);
    }
    
    /**
     * Set smart search config at runtime.
     *
     * @param bool $bool
     * @return $this
     */
    public function smart($bool = true)
    {
        config(['datatables-mongodb.search.smart' => $bool]);
        return $this;
    }    
    
    /**
     * Get config use wild card status.
     *
     * @return bool
     */
    public function isWildcard()
    {
        return !! config('datatables-mongodb.search.use_wildcards', false);
    }
}