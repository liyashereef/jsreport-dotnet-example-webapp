<?php

namespace Modules\Supervisorpanel\Repositories;

use Modules\Supervisorpanel\Models\SiteNoteTask;

class SiteNoteTaskRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $site_note_task_model;

    /**
     * Create a new Repository instance.
     *
     * @param  \App\Models\ShiftJournal $shiftJournalModel
     */
    public function __construct(
        SiteNoteTask $site_note_task_model
        )
    {
        $this->site_note_task_model = $site_note_task_model;

    }

    /**
     * Get the all site note by customer id
     * @param $customer_id
     * @return string
     */
    public function getSiteNoteTask($customer_id, $note_id)
    {
        
    }

    /**
     *
     * Function to save site notes
     * @param Request $request
     * @param integer $customer_id
     * @param integer $note_id - if 0- new create, >0 edit
     * @return integer task id
     */
    public function save($request, $customer_id, $note_id)
    {
        $site_note_request_arr = json_decode($request->getContent());
        $site_note_task_id = null;
        //dd($note_id);
        /*$existing_tasks = (SiteNoteTask::where('site_notes_id',$note_id)->pluck('id')->toArray());
        $submitted_tasks = (array_map('trim',data_get($site_note_request_arr,'task_list.*.task_id')));

        if(count($existing_tasks) > 0){
            $removed_task_arr = array_diff($existing_tasks,$submitted_tasks);
            if(count() > 0){
                foreach($removed_task_arr as $removed_task_id){
                    // This row needs to be deleted
                    $site_note_task = SiteNoteTask::find($removed_task_id)->delete();
                }
            }
        }*/
        foreach($site_note_request_arr->task_list as $task){
            $task_id = $task->task_id;
            $create_site_note_task = (SiteNoteTask::find($task_id) == NULL) ? TRUE : FALSE;
            if(empty($task->task_subject)){
                // subject is empty, no other fields should contain value as it is already validated
                if($create_site_note_task){                
                    // if a newly added row, skip the row
                    continue;
                } else {
                    // This row needs to be deleted - 
                    $site_note_task = SiteNoteTask::find($task_id);
                    $site_note_task->delete();
                    continue;
                }
            }
            $site_note_task = SiteNoteTask::find($task_id);
            if($create_site_note_task){
                $site_note_task = new SiteNoteTask;
                // The user should not be able to update the values 
                // other than status
                $site_note_task->site_notes_id = $note_id;
                $site_note_task->task_name = $task->task_subject;
                $site_note_task->assigned_to = $task->assignee;
                $site_note_task->due_date = $task->due_date;    
                $site_note_task->created_by = \Auth::id();            
            }  
            $site_note_task->status_id = $task->task_status;
            $site_note_task->updated_by = \Auth::id();
            $site_note_task->save();
            $site_note_task_id = $site_note_task->id;
        }        
        return $site_note_task_id;
    }



}
