<?php

namespace App\Exports;

use Modules\Admin\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Modules\Admin\Models\TrackingProcessLookup;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Modules\Timetracker\Models\EmployeeShiftReportEntry;

class VisionExport implements WithColumnFormatting, FromArray, WithHeadings, ShouldAutoSize, WithEvents
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $result = User::with([
            "user_salutations",
            "user_bank",
            "user_tax",
            "user_marital_status",
            "user_benefits"
        ])->whereHas("roles", function ($q) {
            return $q->whereNotIN("name", ["super_admin", "admin","client"]);
        })->whereHas("employee", function ($qry) {
            return $qry->where("employee_no", '!=', null);
        })->orderBy("first_name", "asc")->get();
        return $this->getUserData($result);
    }

    public function headings(): array
    {
        // return [
        //     'Status',
        //     'Entity',
        //     'Emp',
        //     'Last Name',
        //     'First Name',
        //     'SIN',
        //     'Title',
        //     'Address',
        //     'City',
        //     'Prov',
        //     'Pcode',
        //     'Ctry',
        //     'DOB',
        //     'Phone',
        //     'Bank',
        //     'Start Date',
        //     'Sen Date',
        //     'Term Date',
        //     'Pay Det',
        //     'Gend(0=M)',
        //     'Marital(0=Single)',
        //     'Vacation%',
        //     'CPP (1=ded)',
        //     'UIC(1=ded)',
        //     'Prov Wrk',
        //     'Sal/Hr',
        //     'DirDep(0=Y)',
        //     'Bank1',
        //     'Bank2',
        //     'Bank3',
        //     'TD-1 Fed',
        //     'TD-1 Prov',
        //     'Epay Email',
        //     'Epay Ex',
        //     'Vet Status',
        // ];
        return [];
    }


    public function getUserData($result)
    {

        $datatable_rows = array();
        $each_row = [];
        foreach ($result as $key => $each_list) {
            if ($each_list->active == 1) {
                $each_row['status'] = "P";
            } else {
                $each_row['status'] = "I";
            }
            $each_row['entity'] =  "0840";
            $each_row['Emp'] = $each_list->employee->employee_no;
            $each_row['last_name'] = $each_list->last_name;
            $each_row['first_name'] = $each_list->first_name;
            $each_row['sin'] =  $each_list->sin;
            $each_row['title'] = $each_list->user_salutations != null ? $each_list->user_salutations->salutation : "";
            $each_row['address'] = str_replace(",", " ", $each_list->employee->employee_address);
            $each_row['city'] = $each_list->employee->employee_city;
            $each_row['prov'] = "ON";
            $postalCode = $each_list->employee->employee_postal_code;
            $managedPostalcode = "";
            if (strlen($postalCode) > 0) {
                $managedPostalcode = substr($postalCode, 0, 3) . " " . substr($postalCode, 3, strlen($postalCode) - 1);
            }
            $each_row['pcode'] = $managedPostalcode;
            $each_row['ctry'] = "CAN";
            $each_row['dob'] = $each_list->employee->employee_dob !== null ? date("n/j/Y", strtotime($each_list->employee->employee_dob)) : "";
            $emloyeePhone = $each_list->employee->phone;
            $emloyeePhone = str_replace('-', '', $emloyeePhone); // Replaces all spaces with hyphens.
            $emloyeePhone = str_replace('_', '', $emloyeePhone); // Replaces all spaces with hyphens.
            $emloyeePhone = preg_replace('/[^A-Za-z0-9\-]/', '', $emloyeePhone); // Removes special chars.
            $each_row['phone'] = $emloyeePhone;
            // $each_row['bank'] = $each_list->user_bank != null ? ($each_list->user_bank->bank != null ? $each_list->user_bank->bank->bank_name : "") : "";
            $each_row['bank'] = "0003";
            $each_row['start_date'] = $each_list->employee->employee_doj !== null ? date("n/j/Y", strtotime($each_list->employee->employee_doj)) : "";
            $each_row['seniority_date'] = $each_list->user_employments !== null ?  (($each_list->user_employments->continuous_seniority) != "" ? date("n/j/Y", strtotime($each_list->user_employments->continuous_seniority)) : "") : "";
            $each_row['termination_date'] = $each_list->termination_date !== null ? date("n/j/Y", strtotime($each_list->termination_date)) : "";
            $each_row['pay_det'] = $each_list->user_employments !== null ?
                ($each_list->user_employments->customer != null ? $each_list->user_employments->customer->project_number : "") : "";
            $each_row['gender'] = $each_list->gender;
            $each_row['marital'] = $each_list->user_marital_status != null ? $each_list->user_marital_status->apogee_code : "";
            $each_row['vacation'] = $each_list->user_benefits != null ? $each_list->user_benefits->vacation_level : "";
            if ($each_list->user_tax != null) {
                if ($each_list->user_tax->is_cpp_exempt == 1) {
                    $each_row['cpp'] = "0";
                } elseif ($each_list->user_tax->is_cpp_exempt === 0) {
                    $each_row['cpp'] = 1;
                } else {
                    $each_row['cpp'] = "";
                }

                if ($each_list->user_tax->is_uic_exempt == 1) {
                    $each_row['uic'] = "0";
                } elseif ($each_list->user_tax->is_uic_exempt === 0) {
                    $each_row['uic'] = 1;
                } else {
                    $each_row['uic'] = "";
                }
                // if ($each_list->user_tax->is_cpp_exempt >= 0) {
                //     $each_row['cpp'] =
                //         $each_list->user_tax->is_cpp_exempt == 1 ? "0" : 1;
                // } else {
                //     $each_row['cpp'] = "";
                // }
            } else {
                $each_row['cpp'] = "";
                $each_row['uic'] = "";
            }
            $each_row['province_work'] = "ON";
            if ($each_list->user_benefits == null) {
                $each_row['sal_hour'] = "";
            } else {
                if ($each_list->user_benefits->payroll_group != null) {
                    $each_row['sal_hour'] =
                        $each_list->user_benefits->payroll_group->apogee_code;
                } else {
                    $each_row['sal_hour'] = "";
                }
            }

            if ($each_list->user_bank != null) {
                if ($each_list->user_bank->payment_methods != null) {
                    $each_row['dir_dep'] = $each_list->user_bank->payment_methods->apogee_code;
                } else {
                    $each_row['dir_dep'] = "";
                }
            } else {
                $each_row['dir_dep'] = "";
            }


            if ($each_list->user_bank != null) {
                if ($each_list->user_bank->payment_method_id === 1) {
                    $each_row['bank1'] = $each_list->user_bank->bank != null ? $each_list->user_bank->bank->bank_code : "";
                    $each_row['bank2'] = $each_list->user_bank->bank != null ? $each_list->user_bank->transit : "";
                    ;
                    $each_row['bank3'] = $each_list->user_bank->bank != null ? $each_list->user_bank->account_no : "";
                    ;
                } else {
                    $each_row['bank1'] = "";
                    $each_row['bank2'] = "";
                    $each_row['bank3'] = "";
                }
            } else {
                $each_row['bank1'] = "";
                $each_row['bank2'] = "";
                $each_row['bank3'] = "";
            }

            $each_row['td1_fed'] = $each_list->user_tax != null ? $each_list->user_tax->federal_td1_claim : "";
            $each_row['td1_prov'] = $each_list->user_tax != null ? $each_list->user_tax->provincial_td1_claim : "";
            $each_row['epay_mail'] = $each_list->user_tax != null ? $each_list->user_tax->epaystub_email : "";
            if ($each_list->user_tax != null) {
                if ($each_list->user_tax->is_epaystub_exempt === 0) {
                    $each_row['epay_ex'] = "N";
                } elseif ($each_list->user_tax->is_epaystub_exempt == 1) {
                    $each_row['epay_ex'] = "Y";
                } else {
                    $each_row['epay_ex'] = "";
                }
                // if ($each_list->user_tax->is_epaystub_exempt >= 0) {
                //     $each_row['epay_ex'] =
                //         $each_list->user_tax->is_epaystub_exempt == 0 ? "N" : "Y";
                // } else {
                //     $each_row['epay_ex'] = "";
                // }
            } else {
                $each_row['epay_ex'] = "";
            }
            if ($each_list->employee->employee_vet_status === 0) {
                $each_row['veteran_status'] = "N";
            } elseif ($each_list->employee->employee_vet_status == 1) {
                $each_row['veteran_status'] = "Y";
            } else {
                $each_row['veteran_status'] = "";
            }
            array_push($datatable_rows, $each_row);
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
        return [];
    }
}
