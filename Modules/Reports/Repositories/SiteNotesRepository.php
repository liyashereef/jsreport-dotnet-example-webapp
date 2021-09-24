<?php

namespace Modules\Reports\Repositories;

use DB;
use Carbon;
use Modules\Supervisorpanel\Models\SiteNote;
use Modules\Supervisorpanel\Models\SiteNoteTask;

class SiteNotesRepository
{   
    public function __constructor() {

    }

    public function getCompleteSiteNotes($startDate, $endDate) {
        $end = Carbon::parse($endDate)->addDays(1)->format('Y-m-d');
        
        $siteNotes = SiteNote::join('customers', 'site_notes.customer_id', '=', 'customers.id')
                            ->whereBetween('site_notes.created_at',[$startDate, $end])
                            ->where('site_notes.deleted_at', null)
                            ->select(
                                'site_notes.id as site_notes_id',
                                'customers.id as customer_id',
                                'customers.project_number as project_number',
                                'customers.client_name as project_name',
                                DB::raw('DATE(site_notes.created_at) as date'),
                                DB::raw('TIME(site_notes.created_at) as time'),
                                'site_notes.subject as subject',
                                'site_notes.attendees as attendees',
                                'site_notes.location as location')
                                ->orderBy('date','ASC')
                                ->orderBy('time','ASC')
                                ->orderBy('project_number', 'ASC')
                                ->get();

        $siteNotesArray = [];

        foreach ($siteNotes as $key => $value) {
            $task = SiteNoteTask::join('site_note_status_lookups','site_note_tasks.status_id','=','site_note_status_lookups.id')
                                ->join('users', 'site_note_tasks.assigned_to','=','users.id')
                                ->where('site_notes_id','=',$value['site_notes_id'])
                                ->where('site_note_tasks.deleted_at', null)
                                ->select('site_note_tasks.task_name as task_name',
                                        DB::raw('CONCAT(IFNULL(users.first_name,"")," ",IFNULL(users.last_name,"")) as assigned_to'),
                                        'site_note_tasks.due_date as due_date',
                                        'site_note_status_lookups.status as status')
                                ->get();
            foreach ($task as $k => $v) {
                $siteNotesArray[$key][$k]['customer_id'] = $value->customer_id;
                $siteNotesArray[$key][$k]['site_notes_id'] = $value->site_notes_id;
                $siteNotesArray[$key][$k]['project_number'] = $value->project_number;
                $siteNotesArray[$key][$k]['project_name'] = $value->project_name;
                $siteNotesArray[$key][$k]['date'] = $value->date;
                $siteNotesArray[$key][$k]['time'] = Carbon::parse($value->time)->format('H:i');
                $siteNotesArray[$key][$k]['subject'] = $value->subject;
                $siteNotesArray[$key][$k]['attendees'] = $value->attendees;
                $siteNotesArray[$key][$k]['location'] = $value->location;
                $siteNotesArray[$key][$k]['task_name'] = $v->task_name;
                $siteNotesArray[$key][$k]['assigned_to'] = $v->assigned_to;
                $siteNotesArray[$key][$k]['due_date'] = $v->due_date;
                $siteNotesArray[$key][$k]['status'] = $v->status;
            }
        }
        return $siteNotesArray;
    }
}