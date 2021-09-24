<?php

namespace Modules\Supervisorpanel\Repositories;

use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Supervisorpanel\Models\SiteNote;

class SiteNoteRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $siteNoteModel;
    protected $employee_allocation_repository;

    /**
     * Create a new Repository instance.
     *
     * @param  \App\Models\ShiftJournal $shiftJournalModel
     */
    public function __construct(
        SiteNote $site_note_model, 
        EmployeeAllocationRepository $employee_allocation_repository
        )
    {
        $this->site_note_model = $site_note_model;
        $this->employee_allocation_repository = $employee_allocation_repository;

    }

    /**
     * Get single site note by id
     * @param integer $note_id 
     */
    public function getSiteNote($note_id){  
        return SiteNote::with('siteNoteTask')->find($note_id);
    }

    /**
     * Get the all site note by customer id
     * @param $customer_id
     * @return string
     */
    public function getSiteNoteByCustomer($customer_id)
    {
        return $site_note_list = SiteNote::where('customer_id',$customer_id)->orderBy('created_at','desc')->get(); 
    }    
    
    /**
    * Get the all site note dates by customer id
    * @param $customer_id
    * @return string
    */
   public function getSiteNoteDatesByCustomer($customer_id)
   {
       return $site_note_list = SiteNote::where('customer_id',$customer_id)->orderBy('created_at','desc')->pluck('created_at', 'id'); 
   }

    /**
     *
     * Function to save site notes
     * @param Request $request
     * @param integer $customer_id
     * @param integer $note_id - if 0- new create, >0 edit
     * @return void
     */
    public function save($request, $customer_id, $note_id)
    {
        $site_note_request_arr = json_decode($request->getContent());
        $site_note = null;
        if($note_id == 0){
            $site_note = new SiteNote;
        } else{
            $site_note = SiteNote::find($note_id);
        }        
        $site_note->customer_id = $customer_id;
        $site_note->subject = $site_note_request_arr->subject;
        $site_note->attendees = $site_note_request_arr->attendees;
        $site_note->location = $site_note_request_arr->location;
        $site_note->notes = $site_note_request_arr->notes;
        if($note_id == 0){
            $site_note->created_by = \Auth::id();
        }
        $site_note->updated_by = \Auth::id();
        $site_note->save();

        return $site_note->id;

    }



}
