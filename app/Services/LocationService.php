<?php

namespace App\Services;

use App\Models\CustomPermission;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use App\Services\HelperService;

class LocationService
{


    /**
     * @param $address (pincode or address)
     * @return array
     */

    public function getLatLongByAddress($address)
    {
        $google_api_key = config('globals.google_api_curl_key');
        HelperService::googleAPILog('geocode', debug_backtrace()[1]['function']);
        $location_data = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=" . $address . "&sensor=false&key=" . $google_api_key);
        $location_data = json_decode($location_data);

        if (isset($location_data->{'results'}[0])) {
            $data['lat'] = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $data['long'] = $location_data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            $data['postal_code_address'] = $location_data->{'results'}[0]->formatted_address;
        } else {
            $data['lat'] = null;
            $data['long'] = null;
            $data['postal_code_address'] = null;
            if( strtolower(config('app.env')) !== 'prod') {
                $data['response'] = $location_data;
            }
        }
        return $data;
    }

    /**
     *
     * @param $inputs , array of origins and destinations
     * @param $first , if true, only first distance and time object returned
     * inputs example
     * {
     * "destinations": [
     * {
     * "lat": "44.4073190000",
     * "long": "-79.6844340000"
     * },
     * {
     * "lat": "42.9323000000",
     * "long": "-81.2198000000"
     * }
     * ],
     * "origins": [
     * {
     * "lat": 43.6547645,
     * "long": -79.3759018
     * }
     * ]
     * }
     * @return json
     **/

    public function getDrivingDistance($inputs, $first = false)
    {
        $originStr = '';
        $destinationStr = '';
        $result['distanceMatrix'] = [];
        $result['message'] = 'success';
        $result['status'] = true;

        if (sizeof($inputs['origins']) >= 1 && sizeof($inputs['destinations']) >= 1) {
            //Origin lat long array to string.
            foreach ($inputs['origins'] as $key => $origin) {
                $originStr .= $origin['lat'] . ',' . $origin['long'] . '|';
            }
            //Destination lat long array to string.
            foreach ($inputs['destinations'] as $key => $origin) {
                $destinationStr .= $origin['lat'] . ',' . $origin['long'] . '|';
            }
            //Removing `or` symbol from origin and destination string.
            $originStr = rtrim($originStr, '|');
            $destinationStr = rtrim($destinationStr, '|');

            $apiKey = config('globals.google_api_curl_key');
            HelperService::googleAPILog('distancematrix', debug_backtrace()[1]['function']);
            $location_data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $originStr . "&destinations=" . $destinationStr . "&mode=driving&key=" . $apiKey);
            $result['distanceMatrix'] = json_decode($location_data);
            if($first) {
                $result['distanceMatrix'] = $result["distanceMatrix"]->rows[0]->elements[0];
            }
        } else {
            $result['message'] = "Origin/Destination not available";
            $result['status'] = false;
        }

        return $result;

    }

    /**
     * Validates and URL Encodes Canada Postal code
     * @param string $postalCode
     * @return string
     * @throws Exception
     */
    public function urlEncodeCnPostalCode(string $postalCode) {
        $postalCode = strtoupper($postalCode);
        if(!preg_match('/([A-Z]\d[A-Z]( )?\d[A-Z]\d)/i',$postalCode)) {
            throw new Exception("Invalid postal code format");
        }
        $spaceLocation = 3;
        if(substr($postalCode,$spaceLocation,1) !== " ") {
            $postalCode = substr_replace($postalCode, " ", $spaceLocation, 0);
        }
        return str_replace(' ', '%20', $postalCode);
    }
}
