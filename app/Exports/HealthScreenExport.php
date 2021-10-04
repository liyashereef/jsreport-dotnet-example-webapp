<?php

namespace App\Exports;

use App\User;
use Carbon\Carbon;
use Modules\Hranalytics\Models\CandidateJob;
use Maatwebsite\Excel\Concerns\FromArray;
use Modules\Admin\Models\TrackingProcessLookup;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;



class HealthScreenExport implements WithColumnFormatting, FromArray, WithHeadings, ShouldAutoSize, WithEvents
{

    protected $data;

    function __construct($number, $data)
    {

        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return $this->getHealthScreenData($this->data);
    }

    public function headings(): array
    {
        return [
            'Project Number',
            'Project Name',
            'Employee Id',
            'Employee Name',
            'Phone',
            'Email',
            'Date',
            'Area Manager',
            'SignIn Time',
            'Screening completed',
            'Screening passed'

        ];
    }


    public function getHealthScreenData($result)
    {
        $datatable_rows = array();
        $each_row = [];
        foreach ($result as $key => $value) {
            foreach ($value as $shortkey => $shortvalue) {
                $each_row["col1"] = $shortvalue["project_number"];
                $each_row["col2"] = $shortvalue["project_name"];
                $each_row["col3"] = $shortvalue["employee_number"];
                $each_row["col4"] = $shortvalue["employee_name"];
                $each_row["col5"] = $shortvalue["phone"];
                $each_row["col6"] = $shortvalue["email"];
                $each_row["col7"] = $shortvalue["date"];
                $each_row["col8"] = $shortvalue["area_manager"];
                $each_row["col9"] = $shortvalue["sign_in"];
                $each_row["col10"] = $shortvalue["screening_completed"];
                $each_row["col11"] = $shortvalue["screening_passed"];

                array_push($datatable_rows, $each_row);
            }
        }
        return $datatable_rows;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:AH1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DATETIME,
            'W' => NumberFormat::FORMAT_DATE_DMYSLASH,
            'AF' => NumberFormat::FORMAT_DATE_DMYSLASH,
        ];
    }
}
