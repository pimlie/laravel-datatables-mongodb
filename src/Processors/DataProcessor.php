<?php
namespace Pimlie\DatatablesMongodb\Processors;

use Yajra\Datatables\Processors\DataProcessor As YajraDataProcessor;

class DataProcessor extends YajraDataProcessor
{
    /**
     * Process data to output on browser
     *
     * @param bool $object
     * @return array
     */
    public function process($object = false)
    {
        $this->output = [];
        $indexColumn  = Config::get('datatables-mongodb.index_column', 'DT_Row_Index');
        foreach ($this->results as $row) {
            $data  = Helper::convertToArray($row);
            $value = $this->addColumns($data, $row);
            $value = $this->editColumns($value, $row);
            $value = $this->setupRowVariables($value, $row);
            $value = $this->removeExcessColumns($value);
            if ($this->includeIndex) {
                $value[$indexColumn] = ++$this->start;
            }
            $this->output[] = $object ? $value : $this->flatten($value);
        }
        return $this->escapeColumns($this->output);
    }
}