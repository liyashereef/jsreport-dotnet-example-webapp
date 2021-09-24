<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hranalytics\Models\BonusEmployeeData;
use Modules\Hranalytics\Models\BonusSettings;
use Modules\Hranalytics\Repositories\BonusRepository;

class BonusController extends Controller
{
    protected $bonusRepository;
    public function __construct()
    {
        $this->bonusRepository = new BonusRepository();
    }
    public function getUserBonusData()
    {
        $bonusData = $this->bonusRepository->getUserBonusData();
        if (count($bonusData)) {
            $successcontent['success'] = true;
            $successcontent['message'] = 'Retrieved successfully';
            $successcontent['data'] = $bonusData;
            $successcontent['code'] = 200;
        } else {
            $successcontent['success'] = false;
            $successcontent['message'] = 'No Data';
            $successcontent['code'] = 406;
        }

        return response()->json($bonusData);
    }
}
