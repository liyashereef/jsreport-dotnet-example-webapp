<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\CandidateSettings;

class SettingsController extends Controller
{

    /**
     * Display Form for adding generic password.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cs = CandidateSettings::find(1);
        return view('admin::settings.genericpassword', compact('cs'));
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
        $data['generic_password'] = $inputs['password'];
        $data['encrypted_password'] = bcrypt($inputs['password']);
        CandidateSettings::updateOrCreate(array('id' => 1), $data);
        return back()->with('password-updated', __('Generic password for all candidates has been successfully created'));
    }

}
