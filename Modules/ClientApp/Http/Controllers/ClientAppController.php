<?php

namespace Modules\ClientApp\Http\Controllers;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\ClientApp\Http\Resources\V1\Customer\CustomerResource;
use Modules\ClientApp\Http\Resources\V1\IncidentReport\IncidentReportResource;
use Modules\ClientApp\Http\Resources\V1\Rating\RatingResource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;
use Modules\Supervisorpanel\Models\IncidentReport;

class ClientAppController extends Controller
{

    protected $userRepository;
    protected $loginCollection;
    protected $helperService;
    protected $attachmentRepository;
    protected $payPeriodRepository;

    public function __construct(
        UserRepository $userRepository,
        HelperService $helperService,
        PayPeriodRepository $payPeriodRepository
    ) {
        $this->userRepository = $userRepository;
        $this->helperService = $helperService;
        $this->payPeriodRepository = $payPeriodRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Exception
     */
    public function customer(Request $request)
    {
        $customerId = null;
        try {
            if (!Auth::user()->hasPermissionTo('view_all_customers_clientapp')) {
                $customerId = CustomerEmployeeAllocation::where('user_id', '=', Auth::user()->id)
                    ->pluck('customer_id')->toArray();
            }
            return CustomerResource::collection(
                Customer::when($customerId, function ($q) use ($customerId) {
                    $q->whereIn('id', $customerId);
                })->orderBy('client_name')->get()
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Exception
     */
    public function incident(Request $request)
    {
        if (($request->has('payPeriodStart') && !empty($request->payPeriodStart)) || ($request->has('payPeriodEnd') && !empty($request->payPeriodEnd))) {
            $request->validate([
                'customerId' => 'required',
                'payPeriodStart' => 'required',
                'payPeriodEnd' => 'required',
            ]);
            $dateFilterApplied = true;
        } else {
            $request->validate([
                'customerId' => 'required',
            ]);
            $dateFilterApplied = false;
        }

        try {
            $customerId = $request->customerId;
            $payPeriodStart = null;
            $payPeriodEnd = null;
            if ($request->has('payPeriodStart') && !empty($request->payPeriodStart)) {
                $payPeriodStart = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($request->payPeriodStart)));
                $payPeriodStart->setTime(0, 0, 0);
            }

            if ($request->has('payPeriodEnd') && !empty($request->payPeriodEnd)) {
                $payPeriodEnd = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($request->payPeriodEnd)));
                $payPeriodEnd->setTime(23, 59, 59);
            }
            $incidentReportQuery = IncidentReport::where('customer_id', $customerId);
            if ($dateFilterApplied) {
                $payPeriods = $this->payPeriodRepository->getAllActivePayPeriodsBetweenDates($payPeriodStart, $payPeriodEnd);
                $incidentReportQuery->whereIn('payperiod_id', $payPeriods);
            }
            $incidentReportQuery->orderBy('created_at', 'desc');
            return IncidentReportResource::collection($incidentReportQuery->get());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Exception
     */
    public function teamProfile(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
        ]);
        try {
            $customerEmployeeAllocationModel = CustomerEmployeeAllocation::where('customer_id', $request->customerId)
                ->with('user')
                ->get();
            $userIdArr = data_get($customerEmployeeAllocationModel, "*.user.id");

            return UserResource::collection(
                User::whereIn('id', $userIdArr)->orderBy('first_name')->get()
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * function to return minimum version
     */
    public function versionCheck()
    {
        return Collection::make(
            [
                "minVersion" => "0.4.0",
                "expiredAt" => "2020-06-15",
            ]
        );
    }

    public function ratings()
    {
        return RatingResource::collection(
            EmployeeRatingLookup::orderBy('score', 'desc')->get()
        );
    }
}
