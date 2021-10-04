<?php

namespace App\Exports;

use Modules\Admin\Models\User;
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



class CompassExport implements
    WithColumnFormatting,
    FromArray,
    WithHeadings,
    ShouldAutoSize,
    WithEvents
{

    protected $result;

    function __construct($result)
    {
        $this->result = $result;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {

        return $this->getVisionData($this->result);
    }

    public function headings(): array
    {
        //Hide header row
        return [];

        return [
            'Entity',
            'Trans Dte',
            'Emp #',
            'Proj #',
            'Code',
            'Fn',
            'Level',
            'Hrs',
            'F1',
            'F2',
            'Chg',
            'F3',
            'F4',
            'Lastname'
        ];
    }


    public function getVisionData($results)
    {
        $outputData = [];
        array_push($outputData, ['blank']);

        foreach ($results as $res) {
            $output = [];
            //Function
            $fn = '';
            $cfn = $res->cpidFunction;
            if ($cfn != null) {
                $fn = $cfn->name;
            }
            //Hours
            $hours = 0;
            if ($res->hours > 0) {
                $hours =  round($res->hours / 60, 2);
            }

            //Trans Date
            $transDate = '';
            if ($res->payperiod_week == 1) {
                $transDate =  $res->payPeriod->week_one_end_date;
            } else {
                $transDate = $res->payPeriod->end_date;
            }

            $output['entity'] =  "0840";
            $output['trans_date'] = Carbon::parse($transDate)->format('n/j/Y');
            $output['emp_no'] = $res->user->trashedEmployee->employee_no;
            $output['site_no'] = $res->customer->project_number;
            $output['code'] = $res->workHourActivityCodeCustomer->code;
            $output['fn'] = $fn;
            $output['level'] = $res->cpidRate->p_standard * 100;
            $output['tot_hrs'] = (string) $hours;
            $output['F1'] = '0';
            $output['F2'] = "";
            $output['Chg'] = "Y";
            $output['F3'] = "";
            $output['F4'] = "C";
            $output['last_name'] = $res->user->last_name;

            array_push($outputData, $output);

            // When the codes 14, 44 and 64 are selected, we should create a duplicate
            // line with the same information except the code.
            // For example: if user chooses code 14, then we should duplicate the row
            // and instead of code 14, we have to enter 22.
            // We have to do this for Federal, Commercial and Provincial.

            $dc = $res->workHourActivityCodeCustomer->duplicate_code;
            if (!empty($dc)) {
                $output['code'] = $dc;
                //Duplicate row
                array_push($outputData, $output);
            }
        }
        return $outputData;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            // AfterSheet::class => function (AfterSheet $event) {
            //     $cellRange = 'A1:AH1'; // All headers
            //     $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
            // },
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }
}
