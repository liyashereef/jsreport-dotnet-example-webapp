<?php

namespace Modules\IdsScheduling\Repositories;

use Modules\IdsScheduling\Models\IdsEntryAmountSplitUp;

class IdsEntryAmountSplitUpRepository
{

      /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\IdsOffice $idsOffice
     */
    public function __construct(IdsEntryAmountSplitUp $idsEntryAmountSplitUp)
    {
        $this->model = $idsEntryAmountSplitUp;
    }


    /**
     * Store a newly created in storage.
    *
    * @param  $request
    * @return object
    */

    public function store($inputs){
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    public function insert($inputs){
        return $this->model->insert($inputs);
    }

    public function updateOrCreate($inputs){
        foreach($inputs as $input){
             $this->model->updateOrCreate([
                'entry_id' => $input['entry_id'],
                'type'=> $input['type']],
                 $input);
        }
    }

    /**
     * Get list
     *
     * @param empty
     * @return array
     */

    public function getByEntryId($entry_id){
        return $this->model->where('entry_id',$entry_id)->get();
     }

    /**
     * Get list of tax
     *
     * @param empty
     * @return object
     */

    public function getTaxByEntryId($entry_id){
        return $this->model->where('entry_id',$entry_id)->where('type', 0)->first();
     }

    /**
     * Get single details
    *
    * @param $id
    * @return object
    */
    public function destroy($id){
        return $this->model->find($id)->delete();
    }

    public function deleteByEntry($inputs){
        return $this->model
        ->where('entry_id',$inputs['entry_id'])
        ->when(isset($inputs['type']) && !empty($inputs['type']), function ($que) use ($inputs) {
            return $que->whereIn('type', $inputs['type']);
        })
        // ->update(['deleted_at' => \Carbon::now()]);
        ->delete();
    }

    public function updateAmountSplitUp($inputs){
        return $this->model
        ->where('ids_entry_id',$inputs['old_ids_entry_id'])
        ->update(['ids_entry_id'=>$inputs['ids_entry_id']]);
    }

    /**
    * Get all totalFee type office_id
    * Fetch total_fee split_ups and deferred revenue
    * @param inputs
    * @return object
    */

    public function getSplitUps($inputs){

        $query = \DB::table('ids_entry_amount_split_ups as split_ups');
        $query->join('ids_entries', 'split_ups.entry_id', '=', 'ids_entries.id');
        $query->groupBy('split_ups.type','ids_entries.ids_office_id');
        $query->select(\DB::raw("SUM(split_ups.rate) as fee"), 'split_ups.type', 'ids_entries.ids_office_id');
        if(isset($inputs) && !empty($inputs['ids_office_id'])){
            $query->whereIn('ids_entries.ids_office_id', $inputs['ids_office_id']);
        }
        if(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date'])){
            $query->whereBetween('ids_entries.slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
        }
        $query->whereNull('split_ups.deleted_at');
        $query->whereNull('ids_entries.deleted_at');
        $query->where(function ($query) use($inputs){
            $query->where('ids_entries.is_client_show_up',1)->orWhere('ids_entries.is_online_payment_received',1);
         });

        $deferedQuery = $query;

        //Total fee splitUps.
        $return['feeSplitUps'] = $query->get();

        //Total deferred billing.
        $return['deferredBilling'] = $deferedQuery->where(function ($query) use($inputs){
            $query->where('is_candidate',1)->orWhere('is_federal_billing',1);
         })
        ->get();
         return $return;
     }

}

