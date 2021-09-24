<?php

namespace Modules\Admin\Repositories;

use DateTime;
use Illuminate\Support\Carbon;
use Modules\Admin\Models\PayPeriod;

class PayPeriodRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  \App\Models\WorkType $workTypeModel
     */
    public function __construct(PayPeriod $payperiod_model)
    {
        $this->model = $payperiod_model;
    }

    /**
     * get Payperiod start date
     * Author : Liya shereef
     * @return type
     */
    public function checkPayperiodstartdate($today)
    {
        //$today = "2019-07-18";
        $periodcount = $this->model::where('start_date', $today)->count();
        return $periodcount;
    }

    /**
     * get Last payperiod id from current date
     * Author : Liya shereef
     * @return type
     */
    public function getLastPayperiod($today)
    {
        $periodrow = $this->model::where('week_one_end_date', '<=', $today)->orderby('start_date', 'desc')->take(1)->first();
        $payperiodid = $periodrow->id;
        if ($payperiodid < 1) {
            $periodrow = $this->model::where('start_date', '<=', $today)->orderby('start_date', 'desc')->take(1)->first();
        }
        return $periodrow;
    }

    /**
     * get payperiod from current date
     * Author : Liya shereef
     * @return type
     */
    public function getPayperiodByDate($date)
    {
        $periodrow = $this->model::where('start_date', '<=', $date)->where('end_date', '>=', $date)->orderby('start_date', 'desc')->take(1)->first();
        return $periodrow;
    }

    /**
     * get payperiod from current date
     * Author : Liya shereef
     * @return type
     */
    public function getPayperioddetailsfromarray($payperiods)
    {
        $periodrow = $this->model::whereIn('id', $payperiods)->orderby('start_date', 'asc')->get()->toArray();
        return $periodrow;
    }

    /**
     * get payperiod from Year
     * Author : Liya shereef
     * @return type
     */
    public function getPayperiodsyearwise($years)
    {
        $periodrow = $this->model::whereIn('year', $years)->orderby('start_date', 'asc')->get()->toArray();
        return $periodrow;
    }

    /**
     * Check whether today is end of a payperiod or not
     * Author : Liya shereef
     * @return type
     */
    public function checkPayperiodflag($today)
    {

        $nextdate = date("Y-m-d", strtotime($today . "+1 days"));
        $previousday = date("Y-m-d", strtotime($today . "-2 days"));
        $periodcount = $this->model::where('end_date', '<=', $nextdate)->where('end_date', '>=', $previousday)->count();
        if ($periodcount > 0) {
            return "true";
        } else {
            return "false";
        }
    }

    /**
     * Get current payperiod object current one - revision
     * @return type
     */
    public function getCurrentPayperiodrevision()
    {
        //return $this->model::select('id')->where('week_one_end_date', '<=', today())->take(1)->orderBy('week_one_end_date','DESC')->where('active', true)->first();

        return $this->model::select('id')->where('start_date', '<=', today())->take(1)->orderBy('week_one_end_date', 'DESC')->where('active', true)->first();
    }

    /**
     * Get current payperiod object current one - revision
     * @return type
     */
    public function getPayperiodcountabovedate()
    {
        //return $this->model::select('id')->where('week_one_end_date', '<=', today())->take(1)->orderBy('week_one_end_date','DESC')->where('active', true)->first();
        return $this->model::select('id')->where('week_one_end_date', '>=', today())->count();
    }

    /**
     * Get current payperiod object current one - revision
     * @return type
     */
    public function getPayperiodcountbelowdate()
    {
        //return $this->model::select('id')->where('week_one_end_date', '<=', today())->take(1)->orderBy('week_one_end_date','DESC')->where('active', true)->first();
        return $this->model::select('id')->where('end_date', '<', today())->count();
    }

    /**
     * Get current payperiod object
     * @return type
     */
    public function getCurrentPayperiod()
    {
        return $this->model::select('id')->where('start_date', '<=', today())->where('end_date', '>=', today())->where('active', true)->first();
    }

    /**
     * Get payperiod details of last n from current date
     * @param type $n
     * @return type
     */
    public function getLastNPayperiod($n)
    {
        return $this->model::orderby('end_date', 'desc')->where('end_date', '<=', today())->where('active', true)->limit($n)->get();
    }

    /**
     * Get payperiod details corresponsing to week one end date
     *
     * @return type
     */
    public function getCurrentPayPeriodextended()
    {

        $payperiod_obj = $this->model::whereDate('end_date', '>=', date("Y-m-d"))->whereDate('start_date', '<=', date("Y-m-d"))->take(1)->Where('active', true)->orderby('start_date', 'desc')->first();
        return $payperiod_obj;
    }

    /**
     * Get payperiod details of last n from current date
     * @param type $n
     * @return type
     */
    public function getLastNPayperiodWithCurrent($n = null)
    {
        $payperiod_obj = $this->model::orderby('start_date', 'desc')->where('start_date', '<=', today())->where('active', true);
        if (isset($n)) {
            $payperiod_obj->limit($n);
        }
        return $payperiod_obj->get();
    }

    /**
     * FOR APP-Get PayPeriod List
     *
     * @param  \App\Models\PayPeriod  $payperiod
     * @return resultset
     */
    public function getAllActivePayPeriods()
    {
        $all_active_payperiods = PayPeriod::select('id', 'year', 'pay_period_name', 'short_name', 'start_date', 'end_date')
            ->whereActive(true)
            ->get();
        return $all_active_payperiods;
    }

    public function getAllActivePayPeriodsbelowdate()
    {
        $all_active_payperiods = PayPeriod::orderBy('start_date', 'DESC')->select('id', 'year', 'pay_period_name', 'short_name', 'start_date', 'end_date')
            ->whereActive(true)
            ->where('start_date', '<=', date("Y-m-d"))
            ->get();
        return $all_active_payperiods;
    }

    public function getAllActivePayPeriodsabovedate()
    {
        $all_active_payperiods = PayPeriod::orderBy('start_date', 'asc')->select('id', 'year', 'pay_period_name', 'short_name', 'start_date', 'end_date')
            ->whereActive(true)
            ->where('end_date', '>=', date("Y-m-d"))
            ->orderby('start_date', 'asc')->get();
        return $all_active_payperiods;
    }

    /**
     * FOR APP-Get last 2 and current and past 2  PayPeriod List
     *
     * @param  \App\Models\PayPeriod  $payperiod
     * @return resultset
     */
    public function getRecentPeriods($past = 3, $currentPlusFuture = 2)
    {
        $last_payperiods = [];
        $past_payperiods = [];
        $current_payperiods = [];

        $last_payperiods = collect(PayPeriod::select('id', 'year', 'pay_period_name', 'short_name', 'start_date', 'end_date')
                ->where('start_date', '<', today())
                ->orderby('start_date', 'desc')
                ->whereActive(true)
                ->take($past)
                ->get())->reverse();

        $past_payperiods = collect(PayPeriod::select('id', 'year', 'pay_period_name', 'short_name', 'start_date', 'end_date')
                ->where('start_date', '>=', today())
                ->orderby('start_date')
                ->whereActive(true)
                ->take($currentPlusFuture)
                ->get());

        $payperiods = collect();
        $payperiods = $last_payperiods->merge($current_payperiods);
        $payperiods = $payperiods->merge($past_payperiods);
        return $payperiods;
    }

    /**
     * Function to get the pastCurrentFuturePayPeriod lookups
     * @param  string  $time_period [Past or Future or Both]
     * @param  integer $no_of_years [Future and Past Years]
     * @return array
     */
    public function getPastCurrentFuturePayPeriod($time_period = null, $no_of_years = 1)
    {
        $current_year = date("Y");
        $past_year = $current_year - $no_of_years;
        $future_year = $current_year + $no_of_years;
        $past_current_future_payperiods = $this->model->where('active', true);

        if (!isset($time_period)) {
            //Get Last N payperiods name List
            $past_current_future_payperiods->orderby('start_date', 'desc')->where('start_date', '<=', today())->limit($no_of_years);
        } else if ($time_period == PAST_PAYPERIOD) {
            $past_current_future_payperiods->orderby('pay_period_name', 'asc')->where([['year', '>=', $past_year], ['year', '<=', $current_year]]);
        } else if ($time_period == FUTURE_PAYPERIOD) {
            $past_current_future_payperiods->orderby('pay_period_name', 'asc')->where([['year', '>=', $current_year], ['year', '<=', $future_year]]);
        } else {
            $past_current_future_payperiods->orderby('start_date', 'desc')->where([['year', '>=', $past_year], ['year', '<=', $future_year]]);
        }

        return $past_current_future_payperiods->pluck('pay_period_name', 'id')->toArray();
    }

    /**
     * Get in between payperiod details
     * @param payperiod_start
     * @param payperiod_end
     * @return array
     */
    public function getPayperiodRange($payperiod_start, $payperiod_end)
    {
        return $this->model::select('id')->where('start_date', '>=', $payperiod_start)
            ->where('start_date', '<=', $payperiod_end)->orderby('start_date', 'asc')->pluck('id')->toArray();
    }

    /**
     * Get in between payperiod details
     * @param payperiod_start
     * @param payperiod_end
     * @return array
     */
    public function getPayperiodRangeAll($payperiod_start, $payperiod_end)
    {
        return $this->model::where('start_date', '>=', $payperiod_start)
            ->where('start_date', '<=', $payperiod_end)->orderby('start_date', 'asc')->get();
    }

    /**
     * Get payperiod name
     * @param id
     */
    public function getShortPayperiodName($id)
    {
        return $this->model::select('short_name')->where('id', $id)->first();
    }

    public function getPayperiodStart($id)
    {
        return $this->model::select('start_date')->where('id', $id)->first();
    }

    /**
     * Get all payperios list
     *
     * @return resultset
     */
    public function getAllPayPeriodList()
    {
        $all_payperiods = PayPeriod::whereActive(true)->orderby('start_date', 'asc')->pluck('short_name', 'start_date')->toArray();
        return $all_payperiods;
    }

    public function getAllPayPeriods()
    {
        return PayPeriod::whereActive(true)->orderby('start_date', 'asc')->get();
    }

    public function getAllPayPeriodListWithNameAndYear()
    {
        $all_payperiods = PayPeriod::whereActive(true)->orderby('start_date', 'asc')->select('id','year','pay_period_name','short_name', 'start_date')->get();
        return $all_payperiods;
    }

    public function yearToDatePayperiod()
    {
        $startDate = new DateTime('first day of January');
        $start = $startDate->format('Y-m-d');
        return $this->model::select('id')->where('start_date', '>=', $start)->where('end_date', '<=', today())->where('active', true)->pluck('id')->toArray();
    }

    /**
     * Get in between payperiod details
     * @param payperiod_start
     * @param payperiod_end
     * @return array
     */
    public function getPayperiodIdsInRange($payperiod_start, $payperiod_end)
    {
        return $this->model::select('id')->where('start_date', '>=', $payperiod_start)
            ->where('end_date', '<=', $payperiod_end)->orderby('start_date', 'asc')->pluck('id')->toArray();
    }

    /**
     * Get in between payperiod with compairing start_date
     * @param payperiod_start
     * @param payperiod_end
     * @return array
     */
    public function getPayperiodIdArrayInRange($payperiod_start, $payperiod_end)
    {
        return $this->model::whereBetween('start_date', [$payperiod_start, $payperiod_end])->orderby('start_date', 'asc')->pluck('id')->toArray();
    }

    public function getPayperiodByArray($payperiods)
    {
        $payperiods = $this->model->select('id', 'start_date', 'end_date')->whereIn('id', $payperiods)->orderBy('start_date', 'asc')->get()->toArray();
        return $payperiods;
    }

    public function getAllActivePayPeriodsBetweenDates($startDate, $endDate)
    {
        $payperiods = PayPeriod::orderBy('start_date', 'DESC')
        ->select('id', 'year', 'pay_period_name', 'short_name', 'start_date', 'end_date','week_one_end_date', 'week_two_start_date')
        //                ->whereActive(true)
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->get();
        return $payperiods;
    }

    public function getPayperiodById($id)
    {
        return $this->model::find($id);
    }

    /**
     * get payperiod week by date
     * @return type
     */
    public function getPayperiodWeekByDate($date)
    {
        $periodrow = $this->model::where('start_date', '<=', $date)->where('end_date', '>=', $date)->orderby('start_date', 'desc')->take(1)->first();
        if (!empty($periodrow)) {
            $weekOneEndDateTimeString = strtotime($periodrow->week_one_end_date);
            $selectedDateTimeString = strtotime($date);
            if ($selectedDateTimeString > $weekOneEndDateTimeString) {
                return 2;
            } else {
                return 1;
            }
        }
        return false;
    }

    /**
     * Get active payperiods overlapping a date range
     *
     * @param $start
     * @param $end
     * @return mixed
     */
    public function getAllActivePayPeriodsByDate($start, $end)
    {
        $payperiods = PayPeriod::orderBy('start_date', 'DESC')
            ->whereActive(true)
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->get();
        return $payperiods;
    }

    /**
     * Get last N payperiods by week two date
     *
     * @param $date
     * @param $n
     * @return mixed
     */
    public function getLastNthPayPeriodsByEndDate($date, $n = 5)
    {
        return $this->model::where('start_date', '<=', $date)->orderby('end_date', 'desc')->take($n)->get();
    }

     /**
     * Get previous N payperiods by date
     *
     * @param $date
     * @param $n
     * @return mixed
     */
    public function getPreviousNthPayPeriodsByDate($date, $n)
    {
        return $this->model::where('end_date', '<', $date)->orderby('start_date')
        ->when($n!=null,function($query) use($n){
            return $query->take($n);
        })
        ->get();
    }

    public function getPastPayPeriodsByDate($date, $n){
        return $this->model::where('start_date', '>=', $date)->orderby('start_date')
        ->when($n!=null,function($query) use($n){
            return $query->take($n);
        })
        ->get();
    }

    /**
     * Get previous payperiod details
     * @return array
     */
    public function getPreviousWeek()
    {
        $payperiodDate = Carbon::now()->format('Y-m-d');
        $currentWeek = $this->getPayperiodWeekByDate($payperiodDate);
        $output = ['ppid' => null, 'week' => null];

        if ($currentWeek == 1) {
            $payPeriodObject = $this->getRecentPeriods(1, 1);
            if (!empty($payPeriodObject)) {
                $payperiodId = isset($payPeriodObject) ? $payPeriodObject[0]->id : null;
                $output['ppid'] = $payperiodId;
                $output['week'] = 2;
            }
        } else {
            $payPeriodObject = $this->getCurrentPayperiod();
            if (!empty($payPeriodObject)) {
                $payperiodId = isset($payPeriodObject) ? $payPeriodObject->id : null;
                $output['ppid'] = $payperiodId;
                $output['week'] = 1;
            }
        }
        return $output;
    }
}
