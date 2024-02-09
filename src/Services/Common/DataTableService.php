<?php

namespace App\Services\Common;

use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;

class DataTableService
{
    private $dataTableFactory;

    public function __construct(DataTableFactory  $_dataTableFactory) {
        $this->dataTableFactory           = $_dataTableFactory;
    }

    /**
     * @param $dataTableType
     * @param $options
     * @return DataTable
     */
    public function createDataTable($dataTableType, array $options = []): DataTable
    {
        return $this->dataTableFactory->createFromType($dataTableType, $options);
    }
}