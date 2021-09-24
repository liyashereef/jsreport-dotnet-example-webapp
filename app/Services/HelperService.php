<?php

namespace App\Services;

use App\Models\CustomPermission;
use App\Models\Module;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;

class HelperService
{

    /**
     * Function for return response.
     *
     * @param [type] $result
     * @return void
     */
    public function returnTrueResponse($result = null)
    {
        if (null == $result) {
            return array('success' => true);
        } else {
            return array('success' => true, 'result' => $result);
        }
    }

    /**
     * Function for return response.
     *
     * @return array
     */
    public function returnFalseResponse($e = null)
    {
        if ($e == null) {
            return array('success' => false);
        } elseif (is_string($e)) {
            return array('success' => false, 'error' => $e);
        } else {
            return array('success' => false, 'error' => (config('app.debug')) ? ($e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()) : ("Something Went Wrong"));
        }
    }

    /**
     * Function to convert "snake_case" to "Title Case"
     * @param String $input_str
     *  - String in <code>snake_case</code>
     * @return String
     *  - String in <code>Sentence Case</code>
     */
    public static function snakeToTitleCase($input_str)
    {
        return ucwords(str_replace("_", " ", $input_str));
    }

    /**
     * Function to convert to snake_case from sentence case
     * @param String $input_str
     *  - String in <code>sentence case</code>
     * @return String
     *  - String in <code>snake_case</code>
     */
    public function getSnakeCase($input_str)
    {
        return strtolower(str_replace(" ", "_", $input_str));
    }

    /**
     * Function to change camel case array key to snake case
     * @param $arrCamelCaseKey
     * @return array
     * @throws \Exception
     */
    public function keySnakeCase($arrCamelCaseKey)
    {
        if (!is_array($arrCamelCaseKey)) {
            throw new \Exception("Array expected");
        }
        $arr_snake_case_key = array();
        $arr = array_map('snake_case', array_keys($arrCamelCaseKey));
        $i = 0;
        foreach ($arrCamelCaseKey as $key => $value) {
            $arr_snake_case_key[$arr[$i]] = $value;
            $i++;
        }
        return $arr_snake_case_key;
    }

    /**
     * To get a unique code
     *
     * @param integer $sequential_id
     * @param array $code
     * @param array $arr_strings
     * @param integer $count_letters
     * @param string $seperator
     * @return void
     */
    public function getUniqueReferenceCode($sequential_id = 0, $arr_strings = [], $count_letters = 3, $seperator = '-', $code = ['CGL'])
    {
        foreach ($arr_strings as $each_string) {
            $code[] = substr($this->sanitiseString($each_string), 0, $count_letters);
        }
        $code[] = $sequential_id;
        $unique_code = strtoupper(implode($seperator, $code));
        return $unique_code;
    }

    /**
     * Generate a random - unique alphanumeric string
     *  - uniqueness is not guaranteed
     * @param string $prefix
     * @return string
     */
    public function uniqueGen(string $prefix = ''): string
    {
        return uniqid($prefix) . ((int)floor(microtime(true)));
    }

    /**
     * Remove spaces,Special Characters and multiple hyphens
     *
     * @param  $string
     * @return $string
     */
    public function sanitiseString($string)
    {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '', $string); // Replaces multiple hyphens with single one.
    }

    /**
     * Get permission id by name
     */
    public static function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }

    /**
     * Get module id by name
     */
    public static function getModuleId($module_name)
    {
        $module_id = Module::where('name', $module_name)->value('id');
        return $module_id;
    }

    /** For Dashboard Customer wise filter
     * Check customer_ids on session
     * @Imputs session value
     * @responce array
     */

    public function getCustomerIds()
    {
        //TODO  revove  customer_ids and session allotmant
        //  $customer_ids = [5,6];
        //  session()->put('customer_ids',$customer_ids);
        if (session()->has('customer_ids') && !empty(session()->has('customer_ids'))) {
            return session()->get('customer_ids');
        }
        return [];
    }

    /** For Dashboard Customer wise filter
     * Check customer_ids on session
     * @Imputs session value
     * @responce array
     */

    public function getFMDashboardFilters()
    {

        $fromdate = new Carbon(date('Y-m-d', strtotime('-30 days')));
        $data['from_date'] = $fromdate->startOfDay();
        $data['to_date'] = Carbon::now()->endOfDay();

        $fm_dashboard_customer_ids = [];
        if (session()->has('fm_dashboard_customer_ids') && !empty(session()->has('fm_dashboard_customer_ids'))) {
            $fm_dashboard_customer_ids = session()->get('fm_dashboard_customer_ids');
        }

        if (session()->has('from_date') && !empty(session()->has('from_date'))) {
            $fromdate = new Carbon(session()->get('from_date'));
            $data['from_date'] = $fromdate->startOfDay();
        }

        if (session()->has('to_date') && !empty(session()->has('to_date'))) {
            $todate = new Carbon(session()->get('to_date'));
            $data['to_date'] = $todate->endOfDay();
        }

        $data['customer_ids'] = $fm_dashboard_customer_ids;

        return $data;
    }

    public static function formatedTimeString($timeString)
    {
        $arr = explode(':', $timeString);
        if ($arr > 2) {
            return $arr[0] . ':' . $arr[1];
        }
        return $timeString;
    }

    public function strTimeToSeconds($time)
    {
        $secTime = 0;
        $arr = explode(':', $time);
        if ($arr >= 2) {
            $secTime += ((int)$arr[1] * 60);
            $secTime += ((int)$arr[0] * 3600);
        }
        return $secTime;
    }


    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371000
    ) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * @param $fence_lat
     * @param $fence_lon
     * @param $rad
     * @param $point_lat
     * @param $point_lon
     * @return bool
     */
    function isInsideFence($fence_lat, $fence_lon, $rad, $point_lat, $point_lon)
    {
        // Compare radius of circle
        // with distance of its center
        // from given point
        /*
        if (($point_lat - $fence_lat) * ($point_lat - $fence_lat) +
            ($point_lon - $fence_lon) * ($point_lon - $fence_lon) <=
            $rad * $rad) {
            return true;
        }
        else {
            return false;
        } */

        $distance = $this->haversineGreatCircleDistance($fence_lat, $fence_lon, $point_lat, $point_lon);
        \Log::channel('travelpath')->info($distance . " <-distance - radius-> " . $rad);
        //        dump("flat ".$fence_lat."flng ".$fence_lon." p".$point_lat." ".$point_lon);
        //        dump($distance." <-distance - radius-> ".$rad);
        if ($distance < $rad) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to replace variables in mail template
     */
    public static function replaceText($name, $email_subject, $email_body, $helper_variable)
    {
        $helper_variable['{receiverFullName}'] = $name;
        $mail['subject'] = str_replace(
            array_keys($helper_variable),
            array_values($helper_variable),
            $email_subject
        );
        $mail['body'] = str_replace(
            array_keys($helper_variable),
            array_values($helper_variable),
            htmlspecialchars_decode($email_body)
        );
        return $mail;
    }

    public static function verifyPermissions($permissions)
    {
        //if no permissions  provided pass
        if (empty($permissions)) {
            return true;
        }

        //check the permissions are of type array
        if (!is_array($permissions)) {
            throw new InvalidArgumentException('Permissions must be an array');
        }

        //check all permissions exists in the table
        $availabePermissions = Permission::whereIn('name', $permissions)->get()->pluck('name')->toArray();
        if (count($permissions) !== count($availabePermissions)) {
            $missingPermissions = array_diff($permissions, $availabePermissions);
            throw new Exception('Invalid Permissions: ' . implode(',', $missingPermissions));
        }
        //if the permissions are valid -pass the exection
        return true;
    }

    public static function sanitizeInput($inputVal)
    {
        return htmlentities($inputVal);
    }

    public static function getFormattedDateDiff($date)
    {
        $now = Carbon::now()->toDateString();
        $daysDiff = Carbon::parse($now)->diffInDays($date, false);
        $absMonthsDiff = Carbon::parse($now)->diffInMonths($date);
        $absYearsDiff = Carbon::parse($now)->diffInYears($date);
        $absDaysDiff = abs($daysDiff);

        if ($absDaysDiff == 0) {
            $timeUnit = "today";
        } elseif ($absDaysDiff == 1) {
            $timeUnit = "1 day";
        } elseif ($absDaysDiff < 30) {
            $timeUnit = $absDaysDiff . " days";
        } elseif ($absMonthsDiff <= 1) {
            $timeUnit = "1 month";
        } elseif ($absMonthsDiff < 12) {
            $timeUnit = $absMonthsDiff . " months";
        } elseif ($absYearsDiff <= 1) {
            $timeUnit = "1 year";
        } else {
            $timeUnit = $absYearsDiff . " years";
        }

        return ['formattedDiff' => $timeUnit, 'daysDiff' => $daysDiff];
    }

    public static function expiryDate($date)
    {
        $diffArray = HelperService::getFormattedDateDiff($date);
        $text = ($diffArray['daysDiff'] < 0)
            ? "Expired " . $diffArray['formattedDiff'] . " ago"
            : (
                ($diffArray['daysDiff'] == 0)
                ? "Expires today"
                : "Expiring in " . $diffArray['formattedDiff']);
        return ['daysDiff' => $diffArray['daysDiff'], 'textDiff' => $text];
    }

    public static function getExpiryColor($date)
    {
        $now = Carbon::now()->toDateString();
        if (Carbon::parse($date)->gt($now)) {
            return Carbon::parse($date)->diffInDays($now) > 29 ? 'green' : 'yellow';
        } else {
            return 'red';
        }
    }

    /**
     * Format date from sql time format to "month date, year" format
     * @param $date String datetime string in 'Y-m-d H:i:s' format
     * @return string
     */
    public static function getFormattedDate(string $date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)
            ->toFormattedDateString();
    }

    function convertToHoursMins($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return 0;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public static function generateMachineCode($name)
    {
        return strtolower(uniqid(str_replace(' ', '_', $name . '_')));
    }

    function getNumerics($str)
    {
        preg_match_all('/\d+/', $str, $matches);
        return $matches[0];
    }

    public static function googleAPILog($type, $source)
    {
        Log::channel('googleApi')
            ->info(
                "googleApiLog: " . json_encode([
                    'date' => Carbon::now()->format('Y-m-d'),
                    'time' => Carbon::now()->format('H:i:s'),
                    'type' => $type,
                    'page' => $source
                ])
            );
    }

    public function logApiError(Exception $e)
    {
        //Log in production
        if (config('app.debug') == false) {
            Log::channel('apiError')->info("API Error : " . $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile() . "\n Trace: \n" . $e->getTraceAsString());
        } else {
            //throw exception in dev mode
            throw $e;
        }
    }

    /**
     * to convert hour to minutes
     */
    public function h2m($hours)
    {
        $t = explode(":", $hours);
        $h = $t[0];
        if (isset($t[1])) {
            $m = $t[1];
        } else {
            $m = 0;
        }
        $mm = ($h * 60) + $m;
        return $mm;
    }

    public static function distanceBetweenCordinates($lat1, $long1,$lat2, $long2)
    {
        $theta = $long1 - $long2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $distance = $miles * 1.609344;
        return  $distance;
    }
}
