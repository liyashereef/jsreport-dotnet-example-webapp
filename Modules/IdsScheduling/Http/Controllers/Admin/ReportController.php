<?php

namespace Modules\IdsScheduling\Http\Controllers\Admin;

use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\IdsOfficeRepository;
use Modules\Admin\Repositories\IdsOfficeSlotsBlocksRepositories;
use Modules\Admin\Repositories\IdsOfficeSlotsRepositories;
use Modules\Admin\Repositories\IdsPaymentMethodsRepository;
use Modules\Admin\Repositories\IdsPaymentReasonsRepository;
use Modules\Admin\Repositories\IdsServicesRepository;
use Modules\IdsScheduling\Models\IdsEntries;
use Modules\IdsScheduling\Models\IdsPaymentMethods;
use Modules\IdsScheduling\Repositories\IdsCustomQuestionRepository;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;

class ReportController extends Controller
{

    private $idsCustomQuestionRepository;
    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\IdsServices $idsServices
     */
    public function __construct(
        IdsOfficeRepository $idsOfficeRepository,
        IdsOfficeSlotsRepositories $idsOfficeSlotsRepositories,
        IdsEntriesRepositories $idsEntriesRepositories,
        IdsServicesRepository $idsServicesRepository,
        IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories,
        IdsCustomQuestionRepository $idsCustomQuestionRepository,
        IdsPaymentMethodsRepository $idsPaymentMethodsRepository,
        IdsPaymentReasonsRepository $idsPaymentReasonsRepository,
        MailQueueRepository $mailQueueRepository,
        HelperService $helperService
    ) {
        $this->idsOfficeRepository = $idsOfficeRepository;
        $this->idsOfficeSlotsRepositories = $idsOfficeSlotsRepositories;
        $this->idsEntriesRepositories = $idsEntriesRepositories;
        $this->idsServicesRepository = $idsServicesRepository;
        $this->idsOfficeSlotsBlocksRepositories = $idsOfficeSlotsBlocksRepositories;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->idsCustomQuestionRepository = $idsCustomQuestionRepository;
        $this->helperService = $helperService;
        $this->idsPaymentMethods = new IdsPaymentMethods();
        $this->idsPaymentMethodsRepository = $idsPaymentMethodsRepository;
        $this->idsPaymentReasonsRepository = $idsPaymentReasonsRepository;
    }

    /**
     * Function to view IDS Report Page
     */
    public function getForecastPage()
    {
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        return view('idsscheduling::admin.report-forecast', compact('officeList'));
    }

    /**
     * Free office slot by
     * @param slot_booked_date and office
     */
    public function getOfficeFreeSlot(Request $request)
    {
        $input = $request->all();
        $input['date'] = $request->input('slot_booked_date');
        $input['today'] = false;
        if (strtotime($input['date']) == strtotime(date('Y-m-d'))) {
            $input['today'] = true;
        }
        return $this->idsOfficeSlotsRepositories->getOfficeFreeSlot($input);
    }

    /**
     * Function to Report data generation.
     * @param slot_booked_date start and end.
     */

    public function getServiceReport(Request $request)
    {
        $inputs = $request->all();
        $data = $this->getDateArray($request->all());
        $inputs['slot_booked_date'] = $data['date'];
        $bookingDetails = $this->idsEntriesRepositories->getServiceReport($inputs);
        $balanceFeeDetails = $this->idsEntriesRepositories->getServiceBalanceFeeReport($inputs);
        $services = $this->idsServicesRepository->getAllServices();

        $data['reports'] = [];
        $data['date_wise_total'] = [];
        $dateTotal = [];
        foreach ($services as $key => $service) {
            $data['reports'][$key] = [];
            $data['reports'][$key]['id'] = $service->id;
            $data['reports'][$key]['name'] = $service->name;
            $data['reports'][$key]['date'] = [];

            foreach ($data['date'] as $keyDate => $date) {
                $data['reports'][$key]['date'][$keyDate]['fee'] = 0;
                $data['reports'][$key]['date'][$keyDate]['off_day_class'] = 0;
                if (date('l', strtotime($date)) == 'Saturday' || date('l', strtotime($date)) == 'Sunday') {
                    $data['reports'][$key]['date'][$keyDate]['off_day_class'] = 1;
                }
                $data['date_wise_total'][$keyDate] = 0;
                $dateTotal[$key]['date'][$keyDate] = 0;
                $dateWiseTotal = 0;
                // foreach ($bookingDetails as $keyIndex => $booking) {
                //     if ($booking->slot_booked_date == $date && $service->id == $booking->ids_service_id) {
                //         $dateTotal[$key]['date'][$keyDate] = (float) $booking->service_fee_sum;
                //         // $data['reports'][$key]['date'][$keyDate] = (int)$booking->service_fee_sum;

                //         $data['reports'][$key]['date'][$keyDate]['fee'] = (float) $booking->service_fee_sum;
                //     }
                //     if ($booking->slot_booked_date == $date) {
                //         // $dateWiseTotal = (float) $data['date_wise_total'][$keyDate] + (float) $booking->service_fee_sum;
                //         $data['date_wise_total'][$keyDate] += (float) $booking->service_fee_sum;
                //     }
                // }

                $revenue = $bookingDetails->where('slot_booked_date', $date)->where('ids_service_id', $service->id)->first();
                $balanceFee = $balanceFeeDetails->where('slot_booked_date', $date)->where('ids_service_id', $service->id)->first();
                $fee = 0;
                if (!empty($revenue)) {
                    $fee = (float)$revenue->service_fee_sum;
                }
                if (!empty($balanceFee)) {
                    $balanceFee = $balanceFee->service_fee_sum;
                    if ($balanceFee < 0) {
                        $balanceFee = floatval(str_replace('-', '', $balanceFee));
                        $fee += $balanceFee;
                    }
                }
                $dateTotal[$key]['date'][$keyDate] = $fee;
                $data['reports'][$key]['date'][$keyDate]['fee'] = $fee;

                $dayRevenue = $bookingDetails->where('slot_booked_date', $date)->sum('service_fee_sum');
                $dayBalanceFee = $balanceFeeDetails->where('slot_booked_date', $date)->sum('service_fee_sum');
                // dd($dayRevenue,$dayBalanceFee);
                // $dayFee = 0;
                // if($dayRevenue > 0){
                //     $dayFee = $dayRevenue;
                // }
                if ($dayBalanceFee < 0) {
                    $dayBalanceFee = floatval(str_replace('-', '', $dayBalanceFee));
                }
                $data['date_wise_total'][$keyDate] += $dayRevenue + $dayBalanceFee;

                // dd($date,$service,$bookingDetails);
                $dateWiseTotal = $data['date_wise_total'][$keyDate];
                $dateWiseTotalArray = explode(".", strval($data['date_wise_total'][$keyDate]));
                if (sizeof($dateWiseTotalArray) == 2) {
                    $dateWiseTotal = floatval($dateWiseTotalArray[0] . '.' . substr($dateWiseTotalArray[1], 0, 2));
                }
                $data['date_wise_total'][$keyDate] = $dateWiseTotal;
            }
            $given_rate = array_sum($dateTotal[$key]['date']);
            $givenRateArray = explode(".", strval($given_rate));
            if (sizeof($givenRateArray) == 2) {
                $given_rate = floatval($givenRateArray[0] . '.' . substr($givenRateArray[1], 0, 2));
            }
            $data['reports'][$key]['service_total'] = $given_rate;
            // $data['reports'][$key]['service_total'] = array_sum($data['reports'][$key]['date']);
        }
        unset($data['date']);
        $data['months_forecast'] = $this->getYearForecast($request->all());
        return $data;
    }

    /**
     * Date formating.
     * @param slot_booked_date start and end.
     */

    public function getDateArray($inputs)
    {
        // Date Array
        $result['date'][0] = $inputs['start_date'];
        $index = 0;
        $incrementDate = $inputs['start_date'];
        //    $result['display_date'][0]['name'] = date('l F d, Y', strtotime($incrementDate));
        while (strtotime($inputs['end_date']) >= strtotime($incrementDate)) {
            $result['date'][$index] = $incrementDate;
            //    $result['display_date'][$index] = date('l F d, Y', strtotime($incrementDate));

            //Formated date for Displaying
            $result['display_date'][$index]['name'] = date('l F d, Y', strtotime($incrementDate));

            $result['display_date'][$index]['date'] = date('Y-m-d', strtotime($incrementDate));

            $result['display_date'][$index]['weekdys'] = false;

            if (date('l', strtotime($incrementDate)) == 'Saturday' || date('l', strtotime($incrementDate)) == 'Sunday') {
                $result['display_date'][$index]['weekdys'] = true;
            }

            $result['displayDate'][$index] = date('F d, Y', strtotime($incrementDate));

            $incrementDate = date('Y-m-d', strtotime('+1 day', strtotime($incrementDate)));
            $index++;
        }

        return $result;
    }

    /**
     *  Forecast Report page, Calculates 12 months sum of fees
     * @param start_dates and ids_office_id
     */
    public function getYearForecast($inputs)
    {
        //Finding 12th month of start date

        // $inputs['end_date'] = \Carbon::parse($inputs['start_date'])->addMonths(12)->endOfMonth()->format('Y-m-d');
        // $inputs['start_date'] = \Carbon::parse($inputs['start_date'])->startOfMonth()->format('Y-m-d');

        $inputs['start_date'] = date('Y-01-01', strtotime($inputs['start_date']));
        $inputs['end_date'] = date('Y-12-31', strtotime($inputs['start_date']));

        // $data = $this->idsEntriesRepositories->getYearForecast($inputs);
        $feeForecast = $this->idsEntriesRepositories->getYearForecast($inputs);
        $balanceForecast = $this->idsEntriesRepositories->getYearBalanceForecast($inputs);
        // dd($feeForecast,$balanceForecast);
        //Setting data format displaying.
        $result = [];
        $startFormat = Carbon::parse($inputs['start_date'])->format('Y-m');
        $endFormat = Carbon::parse($inputs['end_date'])->format('Y-m');
        $key = 0;

        //Setting default value for month which have no booking
        while ($endFormat >= $startFormat) {
            //Set defalut value
            $result[$key]['title'] = Carbon::parse($startFormat)->format('M-y');
            $result[$key]['total_fee'] = 0;
            $serviceFee = floatval($feeForecast->where('month_year', $startFormat)->pluck('service_fee_sum')->first());
            $balanceFee = floatval($balanceForecast->where('month_year', $startFormat)->pluck('service_fee_sum')->first());
            if ($balanceFee < 0) {
                $balanceFee = floatval(str_replace('-', '', $balanceFee));
            }
            $result[$key]['total_fee'] = number_format($serviceFee + $balanceFee, 2);


            // foreach ($data as $q => $d) {
            //     $titleFormat = \Carbon::parse($d->month_year)->format('M-y');
            //     $start = \Carbon::parse($startFormat)->format('M-y');
            //     if ($titleFormat == $start) { //Adding sum of fee
            //         $result[$key]['total_fee'] = number_format($d->service_fee_sum);
            //     }
            // }
            $startFormat = Carbon::parse($startFormat)->addMonths(1)->format('Y-m');
            $key++;
        }

        return $result;
    }

    /**
     * Function to view IDS analytics Report Page
     */
    public function getAnalyticsPage()
    {
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        $questions = $this->idsCustomQuestionRepository->getAllAnaswerd();
        return view('idsscheduling::admin.report-analytics', compact('officeList', 'questions'));
    }
    /**
     * Fetch analytics report data.
     * @param start_date, end_date and ids_office_id (array)
     */

    public function getAnalyticsReport(Request $request)
    {
        $bookings = $this->idsEntriesRepositories->getAnalytics($request->all());
        return $this->setAnalyticsData($bookings);
    }

    /**
     * Format analytics report data.
     * @param bookingDatas(object)
     * @return  array
     */

    public function setAnalyticsData($bookingDatas)
    {
        $result = [];
        foreach ($bookingDatas as $key => $bookings) {
            $scheduledAt = null;
            $delta = 0;
            if (!empty($bookings->IdsOfficeSlots)) {
                // $scheduledAt = \Carbon::parse($bookings->slot_booked_date)->format('Y-m-d').' '.\Carbon::parse($bookings->IdsOfficeSlots->start_time)->format('H:i:s');
                // $created = \Carbon::parse($bookings->created_at);
                // $scheduledAt = \Carbon::parse($scheduledAt);

                $scheduledAt = Carbon::parse(Carbon::parse($bookings->slot_booked_date)->format('Y-m-d'));
                $created = Carbon::parse(Carbon::parse($bookings->created_at)->format('Y-m-d'));

                $delta = $created->diffInDays(Carbon::parse($scheduledAt));
            }
            $sheduleStartTime = null;
            if (!empty($bookings->IdsOfficeSlots)) {
                $sheduleStartTime = Carbon::parse($bookings->IdsOfficeSlots->start_time)->format('H:i:s');
            }
            $serviceFee = '';
            $photoFee = '';
            if (!empty($bookings->idsEntryAmountSplitUp)) {
                $serviceFee = collect($bookings->idsEntryAmountSplitUp)->where('type', 1)->pluck('rate')->first();
            } else {
                $serviceFee = $bookings->given_rate;
            }
            if (!empty($bookings->idsPassportPhotoServiceWithTrashed)) {
                $photoFee = collect($bookings->idsEntryAmountSplitUp)->where('type', 2)->pluck('rate')->first();
            }
            $paymentType = '--';
            if (!empty($bookings->IdsPaymentMethods)) {

                if ($bookings->is_online_payment_received == 1) {
                    $isOfflineOrOnline = 'Online-Stripe and ';
                } else {
                    $isOfflineOrOnline = '';
                }
                $paymentType = $isOfflineOrOnline . $bookings->IdsPaymentMethods->full_name;
            } else {
                if ($bookings->is_online_payment_received == 1) {
                    $paymentType = 'Online-Stripe';
                }
            }
            // dd($bookings,$serviceFee, $photoFee,$bookings->given_rate);
            $result[$key] = [
                'id' => $bookings->id,
                'firstName' => $bookings->first_name,
                'lastName' => $bookings->last_name,
                'fullName' => $bookings->first_name . ' ' . $bookings->last_name,
                'email' => $bookings->email,
                'phone' => $bookings->phone_number,
                'transactionAt' => Carbon::parse($bookings->created_at)->format('Y-m-d H:i:s'),
                'transactionDate' => Carbon::parse($bookings->created_at)->format('M d Y'),
                'transactionTime' => Carbon::parse($bookings->created_at)->format('H:i:s'),
                'scheduledAt' => Carbon::parse($bookings->slot_booked_date)->format('Y-m-d') . ' ' . $sheduleStartTime,
                'scheduledDate' => Carbon::parse($bookings->slot_booked_date)->format('M d Y'),
                'scheduledTime' => (!empty($bookings->IdsOfficeSlots)) ? Carbon::parse($bookings->IdsOfficeSlots->start_time)->format('H:i') : null,
                'delta' => $delta,
                'postalCode' => (!empty($bookings->postal_code)) ? $bookings->postal_code : '',
                'service' => (!empty($bookings->IdsServicesWithTrashed)) ? $bookings->IdsServicesWithTrashed->name : '',
                'serviceFee' => (!empty($serviceFee)) ?  '$' . $serviceFee : '',
                'serviceId' => $bookings->ids_service_id,
                'photoService' => (!empty($bookings->idsPassportPhotoServiceWithTrashed)) ? $bookings->idsPassportPhotoServiceWithTrashed->name : '',
                'photoFee' => (!empty($photoFee)) ?  '$' . $photoFee : '',
                'photoServiceId' => $bookings->passport_photo_service_id,
                'office' => (!empty($bookings->IdsOffice)) ? $bookings->IdsOffice->name : '',
                'amount' => (!empty($bookings->given_rate)) ? '$' . $bookings->given_rate : '',
                'noMasksGiven' => (!empty($bookings->no_masks_given)) ? $bookings->no_masks_given : 0,
                'clientShowUp' => (!empty($bookings->is_client_show_up)) ? 'Yes' : 'No',
                'paymentRecieved' => ($bookings->is_payment_received == 1 || $bookings->is_online_payment_received == 1) ? 'Yes' : 'No',
                'paymentType' => $paymentType,
                'paymentReasonId' => (!empty($bookings->ids_payment_reason_id)) ? $bookings->IdsPaymentReasonsWithTrashed->name : '--',
                'paymentReason' => (!empty($bookings->payment_reason)) ? $bookings->payment_reason : '--',

                // 'paymentType'=>(!empty($bookings->idsPaymentMethods))? $bookings->idsPaymentMethods->full_name : '',
                'questionAnswers' => (sizeof($bookings->IdsCustomQuestionAnswers) > 0) ? $bookings->IdsCustomQuestionAnswers : [],
                'amountSplitUp' => (sizeof($bookings->idsEntryAmountSplitUp) > 0) ? $bookings->idsEntryAmountSplitUp : [],
                // 'questionAnswers' => $bookings->IdsCustomQuestionAnswers,

            ];
        }
        // dd($result);
        return $result;
    }

    /**
     * Function to view trends report Page
     */
    public function getTrendsPage()
    {
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        return view('idsscheduling::admin.report-trend', compact('officeList'));
    }

    /**
     * Fetch trends report data.
     * @param start_date, end_date and ids_office_id (array)
     */
    public function getTrendsReport(Request $request)
    {
        // $dateFormat =$this->getDateArray($request->all());
        $inputs = $request->all();
        return $this->setTrendsReportData($this->idsEntriesRepositories->getTrends($inputs), $inputs);
    }

    /**
     * Format analytics report data.
     * @param bookingDatas(object)
     * @return  array
     */

    public function setTrendsReportData($bookingDatas, $inputs)
    {
        $dateFormat = $this->getDateArray($inputs);
        $result = [];
        if (!empty($inputs) && isset($inputs['ids_office_id'])) {
            $officeList = $this->idsOfficeRepository->getByIds($inputs['ids_office_id']);
        } else {
            $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        }

        $trendsData = [];
        $trendsChartData = [];
        foreach ($officeList as $id => $office) {
            $trendsData[$id]['office'] = $office;
            $trendsData[$id]['trendStatus'] = 1;

            $trendsChartData[$id]['locationId'] = $id;
            $trendsChartData[$id]['locationName'] = $office;

            foreach ($dateFormat['date'] as $dateKey => $date) {
                $trendsData[$id]['dateWiseData'][$dateKey]['date'] = $date;
                $trendsData[$id]['dateWiseData'][$dateKey]['count'] = 0;
                $trendsData[$id]['dateWiseData'][$dateKey]['offDayClass'] = 0;
                if (date('l', strtotime($date)) == 'Saturday' || date('l', strtotime($date)) == 'Sunday') {
                    $trendsData[$id]['dateWiseData'][$dateKey]['offDayClass'] = 1;
                }

                foreach ($bookingDatas as $key => $bookings) {
                    if ($bookings->slot_booked_date == $date && $bookings->ids_office_id == $id) {
                        $trendsData[$id]['dateWiseData'][$dateKey]['count'] = $bookings->total_count;
                    }
                }

                $trendsChartData[$id]['dateWiseCount'][$dateKey] = $trendsData[$id]['dateWiseData'][$dateKey]['count'];
            }
        }
        $result['trendsData'] = $trendsData;
        $result['displayDate'] = $dateFormat['display_date'];
        $result['trendsChartData'] = $trendsChartData;
        $result['trendsChartCategories'] = $dateFormat['displayDate'];
        return $result;
    }

    /**
     * Function to view IDS analytics Report Page
     */
    public function getRevenuePage()
    {
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        $paymentMethods = $this->idsPaymentMethodsRepository->getAll()->pluck('full_name', 'id')->toArray();
        $paymentReasons = $this->idsPaymentReasonsRepository->getAll()->pluck('name', 'id')->toArray();
        $services = $this->idsServicesRepository->getAllServices()->pluck('name', 'id')->toArray();

        return view('idsscheduling::admin.report-revenue', compact(
            'officeList',
            'paymentMethods',
            'paymentReasons',
            'services'
        ));
    }

    /**
     * Fetch revenue report data.
     * @param start_date, end_date, is_payment_received
     * @param ids_office_id(array) ids_payment_method_id(array), ids_payment_reason_id(array), ids_service_id(array),
     */

    public function getRevenueReport(Request $request)
    {
        $inputs = $request->all();
        $inputs['is_client_show_up'] = 1;

        $stripeId = data_get($this->idsPaymentMethodsRepository->getByShortName('STRIPE'), 'id');
        unset($inputs['is_online_payment_received']);
        if (
            isset($inputs['ids_payment_method_id']) &&
            $stripeId > 0 &&
            in_array($stripeId, $inputs['ids_payment_method_id'])
        ) {
            $inputs['is_online_payment_received'] = 1;
        }

        $data['revenues'] = $this->idsEntriesRepositories->getReports($inputs);
        unset($inputs['is_client_show_up']);
        $inputs['refund_status'] = [
            config('globals.ids_refund_not_requested'),
            config('globals.ids_refund_rejected')
        ];
        $data['revenues_canceled_or_deleted'] = $this->idsEntriesRepositories->getCanceledOrDeletedEntries($inputs);
        $data['months_revenue'] = $this->getYearRevenue($inputs);
        return $data;
    }

    /**
     *  Forecast Report page, Calculates 12 months sum of fees
     * @param start_dates and ids_office_id
     */
    public function getYearRevenue($inputs)
    {
        //Finding 12th month of start date

        // $inputs['end_date'] = \Carbon::parse($inputs['start_date'])->addMonths(12)->endOfMonth()->format('Y-m-d');
        // $inputs['start_date'] = \Carbon::parse($inputs['start_date'])->startOfMonth()->format('Y-m-d');
        $inputs['start_date'] = date('Y-01-01', strtotime($inputs['start_date']));
        $inputs['end_date'] = date('Y-12-31', strtotime($inputs['start_date']));
        $inputs['is_client_show_up'] = 1;
        $revenueData = $this->idsEntriesRepositories->getYearRevenue($inputs);
        $balanceFees = $this->idsEntriesRepositories->getYearBalanceRevenue($inputs);

        //Setting data format displaying.
        $result = [];
        $startFormat = Carbon::parse($inputs['start_date'])->format('Y-m');
        $endFormat = Carbon::parse($inputs['end_date'])->format('Y-m');
        $key = 0;

        //Setting default value for month which have no booking
        while ($endFormat >= $startFormat) {
            //Set defalut value
            $result[$key]['title'] = Carbon::parse($startFormat)->format('M-y');
            $result[$key]['total_fee'] = 0;
            $revenue = floatval(collect($revenueData)->where('month_year', $startFormat)->pluck('service_fee_sum')->first());
            $balance = floatval(collect($balanceFees)->where('month_year', $startFormat)->pluck('service_fee_sum')->first());
            if ($balance < 0) {
                $balance = floatval(str_replace('-', '', $balance));
            }
            $result[$key]['total_fee'] = number_format($revenue + $balance, 2);

            // foreach ($data as $q => $d) {
            //     $titleFormat = \Carbon::parse($d->month_year)->format('M-y');
            //     $start = \Carbon::parse($startFormat)->format('M-y');
            //     if ($titleFormat == $start) { //Adding sum of fee
            //         $result[$key]['total_fee'] = number_format($d->service_fee_sum);
            //     }
            // }
            $startFormat = Carbon::parse($startFormat)->addMonths(1)->format('Y-m');
            $key++;
        }

        return $result;
    }

    /**
     * Fetch photo revenue report data.
     * @param start_date, end_date, is_passport_photo_service
     * @param ids_office_id(array) ids_payment_method_id(array), ids_payment_reason_id(array), passport_photo_service_id(array),
     */

    public function getPhotoRevenueReport(Request $request)
    {
        $inputs = $request->all();
        $inputs['is_client_show_up'] = 1;

        $stripeId = data_get($this->idsPaymentMethodsRepository->getByShortName('STRIPE'), 'id');
        unset($inputs['is_online_payment_received']);
        if (
            isset($inputs['ids_payment_method_id']) &&
            $stripeId > 0 &&
            in_array($stripeId, $inputs['ids_payment_method_id'])
        ) {
            $inputs['is_online_payment_received'] = 1;
        }

        $data['revenues'] = $this->idsEntriesRepositories->getReports($inputs);
        $inputs['refund_status'] = [
            config('globals.ids_refund_not_requested'),
            config('globals.ids_refund_rejected')
        ];
        $data['revenues_canceled_or_deleted'] = $this->idsEntriesRepositories->getCanceledOrDeletedEntries($inputs);
        return $data;
    }

    public function getAppointmentGeoMap(Request $request)
    {
        $google_api_key = config('globals.google_api_key');
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        $serviceList = $this->idsServicesRepository->getAllServices()->pluck('name', 'id')->toArray();
        $idsEntries = IdsEntries::select(\DB::raw("CONCAT(first_name, ' ',last_name) as full_name"), 'id')->orderBy('first_name', 'ASC')->pluck('full_name', 'id');
        return view('idsscheduling::admin.appointment-geomap', compact('officeList', 'serviceList', 'idsEntries', 'request'));
    }

    public function getAppointmentGeoMapData(Request $request)
    {
        return $this->idsEntriesRepositories->getAppointmentGeoMapData($request->all());
    }
    /**
     * Function to view IDS Refund Report Page
     */
    public function getRefundPage()
    {
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        $paymentMethods = $this->idsPaymentMethodsRepository->getAll()->pluck('full_name', 'id')->toArray();
        $paymentReasons = $this->idsPaymentReasonsRepository->getAll()->pluck('name', 'id')->toArray();
        $services = $this->idsServicesRepository->getAllServices()->pluck('name', 'id')->toArray();

        return view('idsscheduling::admin.report-refund', compact(
            'officeList',
            'paymentMethods',
            'paymentReasons',
            'services'
        ));
    }

    /**
     * Fetch refund report data.
     * @param start_date, end_date,
     * @param ids_office_id(array) ids_payment_method_id(array), ids_payment_reason_id(array), ids_service_id(array),
     */

    public function getRefundReport(Request $request)
    {
        $inputs = $request->all();
        return $this->idsEntriesRepositories->getRefundList($inputs);
    }

    /**
     * Function to view IDS Photo Report Page
     */
    public function getPhotoRevenuePage()
    {
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        $paymentMethods = $this->idsPaymentMethodsRepository->getAll()->pluck('full_name', 'id')->toArray();
        $paymentReasons = $this->idsPaymentReasonsRepository->getAll()->pluck('name', 'id')->toArray();
        $passportPhotoServices = $this->idsServicesRepository->getAllPassportPhotoServices()->pluck('name', 'id')->toArray();

        return view('idsscheduling::admin.photo-revenue-report', compact(
            'officeList',
            'paymentMethods',
            'paymentReasons',
            'passportPhotoServices'
        ));
    }

    /**
     * Function to view IDS Office Revenue Page.
     */
    public function getOfficeRevenuePage()
    {
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        return view('idsscheduling::admin.report-office-revenue', compact('officeList'));
    }

    /**
     * Fetch IDS Office Revenue report data.
     * @param start_date, end_date
     * @param ids_office_id(array)
     */
    public function getOfficeRevenueReport(Request $request)
    {
        $inputs = $request->all();
        $officeRevenue = $this->idsEntriesRepositories->getOfficeRevenue($inputs); //
        return $this->setofficeRevenueData($officeRevenue, $inputs);
    }
    /**
     * Format a Office Revenue report data.
     * @param officeRevenueData(object)
     * @return  array
     */

    public function setofficeRevenueData($data, $inputs)
    {
        $result = [];
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        foreach ($officeList as $key => $row) {
            //Office filter
            if (isset($inputs) && !empty($inputs['ids_office_id'])) {
                $inFilter = in_array($key, $inputs['ids_office_id']);
                if (!$inFilter) {
                    continue;
                }
            }

            $totalRevenue = 0;
            $onlineProcessingFee = 0;
            $netRevenue = 0;
            $deferredBilling = 0;

            //Total revenue object.
            $totalRevenueObj = collect($data['totalFee'])->where('ids_office_id', $key)->first();

            //Sum of refund pending without defered transaction.
            $pendingRefund =  collect($data['refunds'])
                ->where('ids_office_id', $key)
                ->where('refund_status', 1)
                ->where('ids_payment_reason_id',null)
                ->sum('balanceFee');

            //Sum of refund pending of defered transaction.
            $pendingDeferedRefund =  collect($data['refunds'])
                ->where('ids_office_id', $key)
                ->where('refund_status', 1)
                ->where('ids_payment_reason_id','!=',null)
                ->sum('balanceFee');

            //Sum of refund rejected.
            $refundRejected =  collect($data['refunds'])
                ->where('ids_office_id', $key)
                ->whereIn('refund_status', [0, 3])
                ->sum('balanceFee');

            //Sum of refund approved without defered transaction.
            $refundApproved =  collect($data['refunds'])
                ->where('ids_office_id', $key)
                ->where('refund_status', 2)
                ->where('ids_payment_reason_id',null)
                ->sum('balanceFee');

            //Sum of refund approved of defered transaction.
            $refundDeferedApproved =  collect($data['refunds'])
                ->where('ids_office_id', $key)
                ->where('refund_status', 2)
                ->where('ids_payment_reason_id','!=',null)
                ->sum('balanceFee');

            $tax = (float)collect($data['feeSplitUps'])->where('ids_office_id', $key)->where('type', 0)->sum('fee');
            $passportPhotoFee = (float)collect($data['feeSplitUps'])->where('ids_office_id', $key)->where('type', 2)->sum('fee');

            $deferredBilling = collect($data['deferredBilling'])->where('ids_office_id', $key)->where('type', 1)->sum('fee');

            //Removing -ve value to +ve.
            if (!empty($totalRevenueObj)) {
                $totalRevenue = (float)$totalRevenueObj->totalFee;
                $onlineProcessingFee = (float)$totalRevenueObj->online_processing_fee;
            }
            if ($pendingRefund < 0) {
                $pendingRefund = (float)str_replace("-", "", $pendingRefund);
            }
            if ($refundRejected < 0) {
                $refundRejected = (float)str_replace("-", "", $refundRejected);
            }
            if ($refundApproved < 0) {
                $refundApproved = (float)str_replace("-", "", $refundApproved);
            }

            $totalRevenue = $totalRevenue + $pendingRefund + $refundRejected + $refundApproved;
            $totalRevenueArray = explode(".", strval($totalRevenue));
            if (sizeof($totalRevenueArray) == 2) {
                $totalRevenue = floatval($totalRevenueArray[0] . '.' . substr($totalRevenueArray[1], 0, 2));
            }

            if ($pendingDeferedRefund < 0) {
                $pendingDeferedRefund = (float)str_replace("-", "", $pendingDeferedRefund);
            }
            if ($refundDeferedApproved < 0) {
                $refundDeferedApproved = (float)str_replace("-", "", $refundDeferedApproved);
            }

            $refundApproved = $refundApproved + $refundDeferedApproved;
            $pendingRefund = $pendingRefund + $pendingDeferedRefund;

            $netRevenue = $totalRevenue - ($onlineProcessingFee + $refundApproved + $tax);
            $netRevenueArray = explode(".", strval($netRevenue));
            if (sizeof($netRevenueArray) == 2) {
                $netRevenue = floatval($netRevenueArray[0] . '.' . substr($netRevenueArray[1], 0, 2));
            }

            $result[$key] = [
                'officeName' => $row,
                'totalRevenue' => ($totalRevenue == 0) ? '' : number_format($totalRevenue, 2),
                'processingFee' => ($onlineProcessingFee == 0) ? '' : number_format($onlineProcessingFee, 2),
                'passportPhotoFee' => ($passportPhotoFee == 0) ? '' : number_format($passportPhotoFee, 2),
                'refunds' => ($refundApproved == 0) ? '' : number_format($refundApproved, 2),
                'rejectedRefunds' => ($refundRejected == 0) ? '' : number_format($refundRejected, 2),
                'pendingRefund' => ($pendingRefund == 0) ? '' : number_format($pendingRefund, 2),
                'deferredBilling' => ($deferredBilling == 0) ? '' : number_format($deferredBilling, 2),
                'taxes' => ($tax == 0) ? '' : number_format($tax, 2),
                'netRevenue' => ($netRevenue == 0) ? '' : number_format($netRevenue, 2),

            ];
        }

        return $result;
    }
}
