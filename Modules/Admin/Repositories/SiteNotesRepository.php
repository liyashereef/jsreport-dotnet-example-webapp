<?php

namespace Modules\Admin\Repositories;

use DB;
use Carbon;
use Modules\Supervisorpanel\Models\SiteNote;
use Auth;

class SiteNotesRepository {

    public function __constructor() {

    }

    public function getCompleteSiteNotes($startDate, $endDate) {

        $siteNotes = SiteNote::with('customer', 'siteNoteTask')
                ->join('customers', 'site_notes.customer_id', '=', 'customers.id')
                ->join('site_note_tasks', 'site_notes.id', '=', 'site_note_tasks.site_notes_id')
                ->join('site_note_status_lookups', 'site_note_tasks.status_id', '=', 'site_note_status_lookups.id')
                ->join('users', 'site_note_tasks.assigned_to', '=', 'users.id')
                ->whereBetween('site_notes.created_at', [$startDate, $endDate])
                ->where('site_notes.deleted_at', null)
                ->select(
                        'customers.project_number as project_number',
                        'customers.client_name as project_name',
                        DB::raw('DATE(site_notes.created_at) as date'),
                        DB::raw('TIME(site_notes.created_at) as time'),
                        'site_notes.subject as subject',
                        'site_notes.attendees as attendees',
                        'site_notes.location as location',
                        'site_note_tasks.task_name as task_name',
                        DB::raw('CONCAT(IFNULL(users.first_name,"")," ",IFNULL(users.last_name,"")) as assigned_to'),
                        'site_note_tasks.due_date as due_date',
                        'site_note_status_lookups.status as status')
                ->orderBy('date', 'ASC')
                ->orderBy('time', 'ASC')
                ->orderBy('project_number', 'ASC')
                ->get();

        $siteNotesArray = [];

        foreach ($siteNotes as $key => $value) {
            $siteNotesArray[$key]['project_number'] = $value->project_number;
            $siteNotesArray[$key]['project_name'] = $value->project_name;
            $siteNotesArray[$key]['date'] = $value->date;
            $siteNotesArray[$key]['time'] = $value->time;
            $siteNotesArray[$key]['subject'] = $value->subject;
            $siteNotesArray[$key]['attendees'] = $value->attendees;
            $siteNotesArray[$key]['location'] = $value->location;
            $siteNotesArray[$key]['task_name'] = $value->task_name;
            $siteNotesArray[$key]['assigned_to'] = $value->assigned_to;
            $siteNotesArray[$key]['due_date'] = $value->due_date;
            $siteNotesArray[$key]['status'] = $value->status;
        }
        return $siteNotesArray;
    }

    public function getSiteNotesByCustomer($customerIdArray, $widgetRequest = false) {
        $qry = SiteNote::with('customer', 'siteNoteTask')
                ->join('customers', 'site_notes.customer_id', '=', 'customers.id')
                ->join('site_note_tasks', 'site_notes.id', '=', 'site_note_tasks.site_notes_id')
                ->join('site_note_status_lookups', 'site_note_tasks.status_id', '=', 'site_note_status_lookups.id')
                ->join('users', 'site_note_tasks.assigned_to', '=', 'users.id')
                ->where('site_notes.deleted_at', null)
                ->whereIn('site_notes.customer_id', $customerIdArray);

        $qry->select('customers.project_number as project_number',
                        'customers.client_name as project_name',
                        DB::raw('DATE(site_notes.created_at) as date'),
                        DB::raw('TIME(site_notes.created_at) as time'),
                        'site_notes.subject as subject',
                        'site_notes.attendees as attendees',
                        'site_notes.location as location',
                        'site_note_tasks.task_name as task_name',
                        DB::raw('CONCAT(IFNULL(users.first_name,"")," ",IFNULL(users.last_name,"")) as assigned_to'),
                        'site_note_tasks.due_date as due_date',
                        'site_note_status_lookups.status as status')
                ->orderBy('date', 'DESC')
                ->orderBy('time', 'DESC')
                ->orderBy('due_date', 'ASC')
                ->orderBy('project_number', 'ASC');
                if ($widgetRequest) {
                    $count = config('dashboard.site_notes_row_limit');
                    $qry->limit($count);
                }
        $siteNotes = $qry->get();
        return $siteNotes;
    }

    public function prepaireSiteNotesArray($siteNotes = []) {
        $siteNotesArray = [];
        foreach ($siteNotes as $key => $value) {
            $siteNotesArray[$key]['project_number'] = $value->project_number;
            $siteNotesArray[$key]['project_name'] = $value->project_name;
            $siteNotesArray[$key]['date'] = '<span class="hidden_date_span">' . $value->date . '</span>' . date_format(date_create($value->date), "F d, Y");
            $siteNotesArray[$key]['time'] = date_format(date_create($value->time), "h:m A");
            $siteNotesArray[$key]['subject'] = $value->subject;
            $siteNotesArray[$key]['attendees'] = $value->attendees;
            $siteNotesArray[$key]['location'] = $value->location;
            $siteNotesArray[$key]['task_name'] = $value->task_name;
            $siteNotesArray[$key]['assigned_to'] = $value->assigned_to;
            $siteNotesArray[$key]['due_date'] = '<span class="hidden_date_span">' . $value->due_date . '</span>' . date_format(date_create($value->due_date), "F d, Y");
            $siteNotesArray[$key]['status'] = $value->status;

            $bgRowColor = $value->status;
            if ($bgRowColor == 'Opened') {
                $siteNotesArray[$key]['_bg_color'] = "#ff9999 !important";
            } elseif ($bgRowColor == 'In Progress') {
                $siteNotesArray[$key]['_bg_color'] = "#ffe690 !important";
            } else {
                $siteNotesArray[$key]['_bg_color'] = "rgba(36, 169, 66, 0.62) !important";
            }
        }
        return $siteNotesArray;
    }

}
