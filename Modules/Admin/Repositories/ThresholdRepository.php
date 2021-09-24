<?php


namespace Modules\Admin\Repositories;
use DB;
use  Modules\Admin\Models\ScheduleSettings;


class ThresholdRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ThresholdRepository instance.
     *
     * @param  \App\Modules\Admin\Models\ScheduleSettings $ScheduleSetting
     * 
     */
    
    public function __construct(ScheduleSettings $scheduleSettings)
    {
        $this->scheduleSettingsModel=$scheduleSettings;
        
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */

    
    public function getScheduleSettingsData(){
        $scheduleSettingsData =  $this->scheduleSettingsModel->select('id','weekly_threshold', 'bi_weekly_threshold')->orderBy('id')->first();
        return $scheduleSettingsData;

    }

    public function saveScheduleSettings($scheduleSettingsData){
        return $scheduleSettingsData->save(); 
        }
       
}

