<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\TimesheetApprovalConfiguration;
use Modules\Admin\Models\TimesheetApprovalRatingConfiguration;
use Carbon\Carbon;
use App\Services\HelperService;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Models\User;
use Illuminate\Support\Facades\DB;
class TimesheetConfigurationRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  \App\Models\TimesheetApprovalConfiguration $CustomerShiftsModel
     */
    public function __construct(TimesheetApprovalRatingConfiguration $timesheetApprovalRatingConfiguration,TimesheetApprovalConfiguration $timesheetApprovalConfiguration,HelperService $helperService)
    {
        $this->model = $timesheetApprovalConfiguration;
        $this->helperService = $helperService;
        $this->timesheetApprovalRatingConfiguration=$timesheetApprovalRatingConfiguration;

    }

    /**
     * Get  CustomerShifts list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $data = $this->model
           ->select('*',
           \DB::raw("TIME_FORMAT(time, '%h:%i %p') as time")
        )->get();

        return $data;
    }

    public function getList()
    {
        return $this->model->orderBy('id', 'asc')->pluck('id', 'time','day')->toArray();
    }

    public function get($id)
    {
        $data = $this->model->find($id);
        $data->time = Carbon::createFromFormat('H:i:s', $data->time)->format('g:i A');

        return $data;

    }


    public function save($data)
    {
        DB::table('timesheet_approval_rating_configurations')->truncate();
        $arrLength=sizeof($data);
        $table = TimesheetApprovalConfiguration::all();

        if($table->isEmpty()){
            $result = new TimesheetApprovalConfiguration();
            $data['time'] = Carbon::createFromFormat('h:i a', $data['time']);
            $result->id=1;
            $result->time=$data['time'];
            $result->day=$data['day'];
            $result->email_1_time=$data['email_1_time'];
            $result->email_1_time=$data['email_1_time'];
            $result->email_2_time=$data['email_2_time'];
            $result->email_3_time=$data['email_3_time'];
            $result->is_previous_week_enabled = (isset($data['is_previous_week_enabled']) ? 1 :0);
            $result->save($data);
            $id=1;
            if($arrLength > 7){
            $this->TimesheetRatingConfiguration($data,$id);
            }
            return response()->json($this->helperService->returnTrueResponse());

        }else{
            $data['time'] = Carbon::createFromFormat('h:i a', $data['time']);
            $data['is_previous_week_enabled'] = (isset($data['is_previous_week_enabled']) ? 1 :0);
            $result = TimesheetApprovalConfiguration::first()->update($data);
            $id = TimesheetApprovalConfiguration::pluck('id');
            if($arrLength > 7){
                $this->TimesheetRatingConfiguration($data,$id);
            }
            return response()->json($this->helperService->returnTrueResponse());
        }


    }

    public function TimesheetRatingConfiguration($data,$id){

            if($id !=null){

                $rowNos = [0,1,2,3,4];
                if ($rowNos != null) {
                    foreach ($rowNos as $row_no) {
                        $valid_until = $data['from_' . $row_no];
                        $rating = $data['rating_' . $row_no];
                        $early = $data['early_' . $row_no];

                        $abs_early=abs($early);
                        $abs_untill=abs($valid_until);

                        if($row_no < 3 ){
                            $type='-1';
                            $difference=$abs_early * $type;
                        }else if($row_no == 3){
                            $type='1';
                            $difference=$abs_early * $type;
                        }
                        else if($row_no == 4){
                            $type='1';
                            $difference=$abs_untill * $type;
                        }

                        if ($early != null) {
                            $ratingData = [
                                'timesheet_approval_configurations_id' => $id[0],
                                'early' => $early,
                                'untill' => $valid_until,
                                'difference' => $difference,
                                'rating' => $rating,
                                'type' => $type,
                            ];
                            TimesheetApprovalRatingConfiguration::updateOrCreate($ratingData);

                        }
                    }

                }
            }

    }



}
