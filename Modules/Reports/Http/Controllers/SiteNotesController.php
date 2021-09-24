<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Supervisorpanel\Models\SiteNote;
use Modules\Reports\Repositories\SiteNotesRepository;   
use DB;

class SiteNotesController extends Controller
{   
    protected $siteNotesRepository;

    public function __construct(SiteNotesRepository $siteNotesRepository) {
        $this->siteNotesRepository = $siteNotesRepository;
    }
    public function siteNotes() {
        return view('reports::sitenotes.customer-site-note');
    }

    public function getSiteNotes(Request $request) {
       $start_date = $request->get('start_date');
       $end_date = $request->get('end_date');
       $siteNotes = $this->siteNotesRepository->getCompleteSiteNotes($start_date, $end_date);
        return view('reports::sitenotes.partials.customer-site-note', compact('siteNotes'));
    }
}
