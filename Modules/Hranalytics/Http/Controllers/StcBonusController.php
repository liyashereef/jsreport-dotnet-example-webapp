<?php

namespace Modules\Hranalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\User;
use Modules\Hranalytics\Models\BonusEmployeeData;
use Modules\Hranalytics\Models\BonusFinalizedData;
use Modules\Hranalytics\Models\BonusSettings;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Repositories\BonusRepository;
use App\Services\HelperService;
use Carbon\Carbon;
use Validator;

class StcBonusController extends Controller
{
    protected $bonusRepository;
    public function __construct()
    {
        $this->bonusRepository = new BonusRepository();
    }
    public function bonusSettings(Request $request)
    {
        $settingsData = null;
        if (isset($request->id)) {
            $settingsData =  BonusSettings::find($request->id);
        } else {
            // $settingsData =  BonusSettings::where("active", 0)->first();
        }
        return view(
            'hranalytics::short-term-contracts.bonussettings',
            compact("settingsData")
        );
    }

    public function bonusPrograms(Request $request)
    {
        $settingsData =  BonusSettings::whereIn("active", [0, 1, 2, 3])->orderBy("active", "asc")->get();
        return view(
            'hranalytics::short-term-contracts.bonusPrograms',
            compact("settingsData")
        );
    }

    public function saveBonussettings(Request $request)
    {
        $returnContent = [
            "success" => false,
            "code" => 401,
            "message" => "Some Internal Issue"
        ];
        $validatedData = Validator::make(
            $request->all(),
            [
                'start_date.*' => 'required',
                'end_date' => [
                    'required',
                    'greater_than_field:start_date',
                    'after_or_equal:' . date('Y-m-d'), // not 'now' string
                ],
                'bonus_amount' => 'required',
                'wagecap_percentage' => 'required',
                'shiftcap_percentage' => 'required',
                'noticecap_percentage' => 'required'

            ],
            array(
                'start_date.*.required' => 'This is required',
                'end_date.*.required' => 'This is required',
                'bonus_amount.required' => 'This is required',
                'wagecap_percentage.required' => 'This is required',
                'shiftcap_percentage.required' => 'This is required',
                'noticecap_percentage.required' => 'This is required',
                'end_date.greater_than_field' => 'Should greater than grace period'

            )
        );
        if ($validatedData->fails()) {
            $messages = $validatedData->messages();
            return response()->json($messages);
        } else {
            $existingBonus = BonusSettings::when(
                $request->start_date != "",
                function ($qry) use ($request) {
                    return
                        $qry->whereBetween("start_date", [
                            Carbon::parse($request->start_date),
                            Carbon::parse($request->end_date)->addHours(23)
                        ])->orwhereBetween("end_date", [
                            Carbon::parse($request->start_date),
                            Carbon::parse($request->end_date)->addHours(23)
                        ]);
                }
            )->when($request->editprogram > 0, function ($q) use ($request) {
                // return $q->where('id', '!=', intval($request->editprogram));
            })->pluck("id")->toArray();
            $existingBonus = count(array_diff($existingBonus, [$request->editprogram]));
            $editProgram = $request->editprogram;
            $activeBonus = BonusSettings::where("active", 1)->count();
            if (
                $activeBonus > 0 && $request->finalize == 0
                && $request->recalculate == 0
                && $editProgram == 0
                && $request->ongoingprogram > 0
            ) {
                if ($request->recalculate == 1) {
                    $ongoingProgram = $request->ongoingprogram;
                    $bonusData = $this->bonusRepository->saveBasicData($ongoingProgram);
                    $returnContent = [
                        "success" => true,
                        "code" => 200,
                        "message" => "Saved Successfully"
                    ];
                }
            } else if ($existingBonus > 0  && $request->recalculate == 0) {
                $returnContent = [
                    "success" => false,
                    "code" => 403,
                    "message" => "Overlapping Bonus Program"
                ];
            } else if ($request->finalize == 0 && $request->recalculate == 0 && $editProgram == 0) {
                $createBonus = BonusSettings::create(
                    [
                        "start_date" => $request->start_date,
                        "end_date" => $request->end_date,
                        "bonus_amount" => $request->bonus_amount,
                        "wagecap_percentage" => $request->wagecap_percentage,
                        "shiftcap_percentage" => $request->shiftcap_percentage,
                        "noticecap_percentage" => $request->noticecap_percentage,
                        "created_by" => Auth::user()->id,
                        "active" => ($request->start_date == date("Y-m-d") && $activeBonus < 1) ? 1 : 0
                    ]
                );
                if ($createBonus) {
                    $poolId = $createBonus->id;
                    $bonusData = $this->bonusRepository->saveBasicData($poolId);
                    $returnContent = [
                        "success" => true,
                        "code" => 200,
                        "message" => "Saved Successfully"
                    ];
                    // if ($request->finalize == 1) {
                    //     BonusFinalizedData::where("bonus_pool_id", $poolId)->delete();
                    //     BonusFinalizedData::insert($bonusData, $poolId);
                    //     $this->bonusRepository->processFinalData($poolId);
                    //     $returnContent = [
                    //         "success" => true,
                    //         "code" => 200,
                    //         "message" => "Saved Successfully"
                    //     ];
                    // } else {
                    //     $returnContent = [
                    //         "success" => true,
                    //         "code" => 200,
                    //         "message" => "Saved Successfully"
                    //     ];
                    // }
                } else {
                    $returnContent = [
                        "success" => false,
                        "code" => 401,
                        "message" => "Some Internal issue"
                    ];
                }
            } else if ($request->finalize == 1 && $editProgram > 0) {
                $createBonus = BonusSettings::updateOrCreate(
                    [
                        "id" => $editProgram
                    ],
                    [
                        "start_date" => $request->start_date,
                        "end_date" => $request->end_date,
                        "bonus_amount" => $request->bonus_amount,
                        "wagecap_percentage" => $request->wagecap_percentage,
                        "shiftcap_percentage" => $request->shiftcap_percentage,
                        "noticecap_percentage" => $request->noticecap_percentage,
                        "updated_by" => Auth::user()->id
                    ]
                );
                if ($createBonus) {
                    $poolId = $editProgram;
                    if ($request->start_date <= date("Y-m-d")) {
                        $bonusData = $this->bonusRepository->saveBasicData($poolId);
                    }
                    if ($request->finalize == 1) {
                        // BonusFinalizedData::where("bonus_pool_id", $poolId)->delete();
                        // BonusFinalizedData::insert($bonusData, $poolId);
                        // $this->bonusRepository->processFinalData($poolId);
                        $returnContent = [
                            "success" => true,
                            "code" => 200,
                            "message" => "Saved Successfully"
                        ];
                    } else {
                        $returnContent = [
                            "success" => true,
                            "code" => 200,
                            "message" => "Saved Successfully"
                        ];
                    }
                }
            } else if ($request->recalculate == 1) {

                $poolId = $request->ongoingprogram;
                $bonusData = $this->bonusRepository->saveBasicData($poolId);
                // BonusFinalizedData::where("bonus_pool_id", $poolId)->delete();
                // BonusFinalizedData::insert($bonusData, $poolId);
                // $this->bonusRepository->processFinalData($bonusData, $poolId);
                $returnContent = [
                    "success" => true,
                    "code" => 200,
                    "message" => "Saved Successfully"
                ];
            }
        }
        return json_encode($returnContent, true);
    }

    public function saveProcessbonusprogram(Request $request)
    {
        $bonusPoolId = $request->id;
        $process = $request->process;
        if ($process == "hold") {
            $process = BonusSettings::find($bonusPoolId)->update([
                "active" => 3
            ]);
        } else if ($process == "activate") {
            $process = BonusSettings::find($bonusPoolId)->update([
                "active" => 1
            ]);
        } else if ($process == "remove") {
            $process = BonusSettings::find($bonusPoolId)->delete();
        }

        if ($process) {
            $returnContent = [
                "success" => true,
                "code" => 200,
                "message" => "Saved Successfully"
            ];
            if ($process == "remove") {
                $returnContent["message"] = "Removed Successfully";
            }
        } else {
            $returnContent = [
                "success" => false,
                "code" => 401,
                "message" => "Not Process"
            ];
        }
        return json_encode($returnContent, true);
    }

    public function bonusModel(Request $request)
    {
        if (isset($request->id)) {
            $currentBonus = BonusSettings::find($request->id);
        } else {
            $currentBonus = BonusSettings::where("active", 0)->first();
        }
        $bonusPoolId = null;
        $customerId = null;
        if ($currentBonus) {
            $bonusPoolId = $currentBonus->id;
        } else {
            $lastBonus = BonusSettings::orderBy("id", "desc")->first();
            if ($lastBonus) {
                $bonusPoolId = $lastBonus->id;
            }
        }
        $bonusSettings = BonusSettings::find($bonusPoolId);
        if ($bonusSettings) {
            $finalizeStatus = $bonusSettings->active;
        }
        $finalizedData = null;
        if ($finalizeStatus == 2) {
            $finalizedData = BonusFinalizedData::where("bonus_pool_id", $bonusPoolId)->get();
        }
        $users = User::where("active", 1)->get();
        $userArray = [];
        foreach ($users as $user) {
            $userRoleName = "";
            if (isset($user->roles)) {
                if ($user->roles->first() != null) {
                    $userRoleName = isset($user->roles) ? ($user->roles->first()->name) : "";
                }
            }
            $userArray[$user->id] = [
                "id" => $user->id,
                "name" => $user->getFullNameAttribute(),
                "roles" => isset($user->roles) ? $user->roles->first() : "",
                "rolename" => isset($user->roles) ? HelperService::snakeToTitleCase($userRoleName) : ""
            ];
        }
        $bonusLogs = null;
        $rankData = null;
        $createdAt = null;
        if ($bonusPoolId > 0) {
            $bonusLogs = BonusEmployeeData::where(
                "bonus_pool_id",
                $bonusPoolId
            )->orderBy('rank_day', 'desc')->first();
            if ($bonusLogs) {
                $rankData = $bonusLogs->rank_data;
                $createdAt = isset($bonusLogs->rank_day) ? Carbon::parse($bonusLogs->rank_day)->format("d M Y") : "";
            }
        }

        return view('hranalytics::short-term-contracts.bonus', compact(
            "bonusLogs",
            "rankData",
            "userArray",
            "bonusSettings",
            "finalizedData",
            "createdAt"
        ));
    }
}
