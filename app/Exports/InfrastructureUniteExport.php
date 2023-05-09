<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;


class InfrastructureUniteExport implements FromArray
{
    protected $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function array(): array
    {
        $exportData = [
            [
                'Unit ID',
                'Unit Name',
                'Total Count',
                'Scanned Count',
                'Not Scanned Count',
                'Percentage',
            ],
        ];

        foreach ($this->results as $result) {
            $exportData[] = [
                $result->unit_id,
                $result->unit_name,
                $result->total_count,
                $result->scanned_count,
                $result->not_scanned_count,
                $result->percentage,
            ];
        }

        return $exportData;
    }
}
