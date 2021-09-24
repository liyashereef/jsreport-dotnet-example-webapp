<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\Employee;
use Modules\Hranalytics\Models\Candidate;

class GeoCodeController extends Controller
{

    public function __construct()
    {
    }

    /**
     * To update the lattitud and longitude
     *
     * @param Request $request
     * @return void
     */
    public function storeLatLng(Request $request)
    {
        $model_type = $request->get('model');
        $id = $request->get('id');
        $lng = $request->get('lng');
        $lat = $request->get('lat');
        try {
            \DB::beginTransaction();
            switch ($model_type) {
                case 'emp':
                    $model = Employee::where(['user_id' => $id]);
                    break;
                case 'cus':
                    $model = Customer::where(['id' => $id]);
                    break;
                case 'cand':
                    $model = Candidate::where(['id' => $id]);
                    break;
            }
            $affetected_rows = $model->update(['geo_location_lat' => $lat, 'geo_location_long' => $lng]);
            //dd($model);
            \DB::commit();
            if ($affetected_rows) {
                return response()->json(array('success' => true, 'affected_rows' => $affetected_rows));
            } else {
                return response()->json(array('success' => false, 'affected_rows' => $affetected_rows));
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }
}
