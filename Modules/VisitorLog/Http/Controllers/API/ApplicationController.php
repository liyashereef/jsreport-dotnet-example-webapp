<?php

namespace  Modules\VisitorLog\Http\Controllers\API;

use Illuminate\Http\Request;

class ApplicationController
{
    public function applicationHealth(Request $request)
    {
        //Validate request
        $request->validate([
            'appId' => 'required|string',
            'version' => 'required|string'
        ]);

        $healthResponse = [
            'deprecated' => false, //app is deprecated
            'latestVersionAvailable' => false, //latest version is available
            'message' => ''
        ];
        //Todo: health logic
        return response()->json($healthResponse, 200);
    }
}
