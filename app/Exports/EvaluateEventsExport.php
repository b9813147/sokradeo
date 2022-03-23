<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EvaluateEventsExport implements WithMultipleSheets
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new EvaluateEventsSheet($this->data);
        $sheets[] = new IndexStatisticsSheet($this->data);

        return $sheets;
    }
}
