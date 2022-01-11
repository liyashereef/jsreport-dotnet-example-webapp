<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Admin\Models\ShiftModuleEntry;
use Log;
use DateTime;

class UpdateShiftModuleEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:updatemoduleentries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To update created_at for shift module entries having 1 sec difference';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ShiftModuleEntry $shiftModuleEntryModel)
    {
        parent::__construct();
        $this->shiftModuleEntryModel = $shiftModuleEntryModel;
       
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
 
        $val =  $this->shiftModuleEntryModel
                ->select( 'created_by','module_id', 'shift_start_date','customer_id')
                ->where('shift_start_date', '!=', null)
              //  ->where('created_by', '=', 115)
              // ->where('customer_id', '=', 247)
                ->where('field_id', '!=', 'Start')
                ->where('field_id', '!=', 'End')
                ->with(['createdUser'=>function($q){
                    $q->select('id','first_name','last_name','email');
                }])
                ->groupBy('created_by','module_id', 'shift_start_date','customer_id')
                ->get()->toArray();

                $temparr =array();
                foreach ($val as $key => $each_val) {
                    $testing  = $this->shiftModuleEntryModel::select('id','module_id','field_id','field_value','created_at','created_by')
                ->where('module_id', '=', $each_val['module_id'])
                ->where('customer_id', '=', $each_val['customer_id'])
                ->where('created_by', '=', $each_val['created_by'])
                ->where('shift_start_date', '=', $each_val['shift_start_date'])
                ->where('field_id', '!=', 'Start')
                ->where('field_id', '!=', 'End')
                ->get()->toArray();
                $temparr[] =  $testing; 
                
                 foreach ($testing as $nkey => $each_test) {
                   if($nkey==0){
                     $temp_created_at = $each_test['created_at'];
                   }else{
                    $new_created_at = $each_test['created_at'];
                    
                    $date = new DateTime( $temp_created_at );
                    $date2 = new DateTime( $new_created_at );
                    $diff = $date2->getTimestamp() - $date->getTimestamp();
                    
                    
                     if($diff==1){
                        Log::channel('moduleEntriesLog')->info("one sec diff".$nkey.'--'. $each_test['created_at'].'-by-'.$each_test['created_by'].'-id-'.$each_test['id'].'<br>');
                        $this->shiftModuleEntryModel::where('id',$each_test['id'])->update(['created_at' => $temp_created_at]);
                     }elseif($diff > 30){
                        $temp_created_at = $each_test['created_at'];
                        Log::channel('moduleEntriesLog')->info("more.........".$nkey.'--'. $each_test['created_at'].'<br>');
                     }else{
                        Log::channel('moduleEntriesLog')->info("same.........".$nkey.'--'. $each_test['created_at'].'<br>');
                     }
                   }

                  }               
                } 
    }
}