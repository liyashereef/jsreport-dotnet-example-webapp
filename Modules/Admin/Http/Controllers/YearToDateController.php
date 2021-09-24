<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Models\YearToDate;
use Illuminate\Http\Request;


class YearToDateController extends Controller
{
    /**
     * Display Form for adding generic password.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cs = YearToDate::first();
        if(is_null($cs))
        {
            $cs = new YearToDate();
            $cs->year_to_date = '01-01';
            $cs->save();
        }
        return view('admin::masters.year-to-date', compact('cs'));
    }

    /**
     * Store a newly created password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $data['year_to_date'] = $inputs['year_to_date'];
        YearToDate::first()->update($data);
        return back()->with('password-updated', __('Start date has been successfully updated'));
    }



}
