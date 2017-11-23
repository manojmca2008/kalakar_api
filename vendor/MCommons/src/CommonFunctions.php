<?php

namespace MCommons;

use Auth\Model\UserSession;
use Home\Model\City;
use Zend\Json\Json;

class CommonFunctions {

    public static $config = array(
        'adapter' => 'Zend\Http\Client\Adapter\Curl',
        'curloptions' => array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 6000
        )
    );

    public static function getUserLocationData() {
        $ip_address = self::getRealIp();
        if ($ip_address == '127.0.0.1') {
            // $ip_address = '199.27.74.134'; //US IP
            $ip_address = '14.141.65.34'; // INDIA IP
        }
        $userSession = new UserSession ();
        $userDetail = $userSession->getUserDetail();
        if (!isset($userDetail ['user_loc_data'])) {
            $user_loc_data = self::getUserLocationByIp($ip_address);
            if (empty($user_loc_data)) {
                return false;
            }

            if ($user_loc_data ['state_code'] == 'Limit Exceeded') {
                $user_loc_data ['city'] = 'San Francisco';
                $user_loc_data ['city_id'] = 23637;
                $user_loc_data ['state_code'] = 'CA';
                $user_loc_data ['time_zone'] = DEFAULT_TIME_ZONE;
                $session = $userSession->setUserDetail('city_id', $user_loc_data ['city_id']);
                $session = $userSession->setUserDetail('city_name', $user_loc_data ['city']);
                $session = $userSession->setUserDetail('state_code', $user_loc_data ['state_code']);

                $session->save();
            }
            $cityModel = new City ();
            $cityData = $cityModel->getCity(array(
                        'columns' => array(
                            'id',
                            'city_name',
                            'state_code',
                            'time_zone'
                        ),
                        'where' => array(
                            'state_code' => $user_loc_data ['state_code'],
                            'city_name' => $user_loc_data ['city'],
                            'status' => 1
                        )
                    ))->current();

            if (!empty($cityData)) {

                $user_loc_data ['city_id'] = $cityData->id;

                if (!empty($cityData->time_zone))
                    $user_loc_data ['time_zone'] = $cityData->time_zone;
                else
                    $user_loc_data ['time_zone'] = DEFAULT_TIME_ZONE;

                $user_loc_data ['city_present'] = true;
                date_default_timezone_set($user_loc_data ['time_zone']);
            } else {
                $user_loc_data ['city_present'] = false;
            }
            $session = $userSession->setUserDetail('user_loc_data', serialize($user_loc_data));
        } else {
            $user_loc_data = unserialize($userSession->getUserDetail('user_loc_data'));
            if (isset($user_loc_data ['time_zone']))
                date_default_timezone_set($user_loc_data ['time_zone']);
        }

        return $user_loc_data;
    }

    public static function getRealIp() {
        if (isset($_SERVER ["HTTP_CLIENT_IP"])) {
            return $_SERVER ["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER ["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER ["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER ["HTTP_X_FORWARDED"])) {
            return $_SERVER ["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER ["HTTP_FORWARDED_FOR"])) {
            return $_SERVER ["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER ["HTTP_FORWARDED"])) {
            return $_SERVER ["HTTP_FORWARDED"];
        } else {
            return $_SERVER ["REMOTE_ADDR"];
        }
    }

    public static function getUserLocationByIp($ip_address) {
        // api url to get user location based on IP Address.
        $userLocationData = array();
        $locationData = @geoip_record_by_name($ip_address);
        if (!empty($locationData) && !empty($locationData ['city']) && !empty($locationData ['state_code'])) {
            $userLocationData ['latitude'] = $locationData ['latitude'];
            $userLocationData ['longitude'] = $locationData ['longitude'];
            $userLocationData ['country'] = $locationData ['country_name'];
            $userLocationData ['state'] = $locationData ['region'];
            $userLocationData ['state_code'] = $locationData ['region'];
            $userLocationData ['city'] = $locationData ['city'];
        } else {
            $query_string = "?GetLocation&template=php3.txt&IpAddress=" . $ip_address;
            $file_path = GOOGLE_MAP_API . $query_string;
            $geo_data = get_meta_tags($file_path);
            $geo_data = (object) $geo_data;
            $userLocationData ['latitude'] = isset($geo_data->latitude) ? $geo_data->latitude : '';
            $userLocationData ['longitude'] = isset($geo_data->longitude) ? $geo_data->longitude : '';
            $userLocationData ['country'] = isset($geo_data->country) ? $geo_data->country : '';
            $userLocationData ['state'] = isset($geo_data->region) ? $geo_data->region : '';
            $userLocationData ['state_code'] = isset($geo_data->regioncode) ? $geo_data->regioncode : '';
            $userLocationData ['city'] = isset($geo_data->city) ? $geo_data->city : '';
        }
        return $userLocationData;
    }

    public function datetostring($date, $resrId = false) {
        if ($resrId) {
            $currentDateTime = StaticOptions::getRelativeCityDateTime(array(
                        'restaurant_id' => $resrId
                    ))->format('Y-m-d H:i');
            $time = strtotime($currentDateTime);
            $difference = $time - strtotime($date);
        } else {
            $difference = time() - strtotime($date);
        }
        $periods = array(
            "second",
            "minute",
            "hour",
            "day",
            "week",
            "month",
            "year",
            "decade"
        );
        $lengths = array(
            "60",
            "60",
            "24",
            "7",
            "4.35",
            "12",
            "10",
            "10"
        );
        $lengthCount = count($lengths);

        for ($j = 0; $difference >= $lengths[$j]; $j++) {
            $difference /= $lengths [$j];

            if ($j == $lengthCount - 1) {
                break;
            }
//            pr($lengths[$j]);
//            pr($difference);
        }

        $difference = round($difference);
        if ($difference != 1) {
            $periods [$j] .= "s";
        }
        $text = "$difference $periods[$j] ago";
        return $text;
    }

    /*
     * Sort array as per key specification.
     * Args:
     * $array:It will contain a array value.
     * $on:This contain keyname on which sorting will be performed.
     * $order:sorting order (ASC/DESC)
     */

    function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    public function replaceDefineString($replacementValue = array(), $string = '') {
        $message = '';
        if (!empty($replacementValue) && $string != "") {
            foreach ($replacementValue as $key => $value) {
                $string = str_replace('{{#' . $key . '#}}', $value, $string);
            }
            return $string;
        }
        return $message;
    }

    function getLnt($zip) {
        $result3 = [];
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($zip) . "&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        //pr($result,1);
        if ($result['status'] === 'OK' && isset($result['results'][0]['address_components'][2]['short_name']) && $result['results'][0]['address_components'][2]['short_name'] === 'New York') {
            $result1[] = $result['results'][0];
            $result2[] = $result1[0]['geometry'];
            $result3[] = $result2[0]['location'];
        }
        return $result3;
    }

    /* Global function to use curl request
     */

    public static function curlRequest($url = false, $method = false) {
        $client = new \Zend\Http\Client($url, self::$config);
        $req = $client->getRequest();
        $response = $client->send($req)->getBody();
        if (empty($response)) {
            return array();
        }
        $data = Json::decode($response, Json::TYPE_ARRAY);
        return $data;
    }

    public function validateEmail($email) {
        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {
            return true;
        }

        return false;
    }

    public function replaceParticulerKeyValueInArray(&$userDineRestaurant) {
        if (!empty($userDineRestaurant)) {
            array_walk($userDineRestaurant, function (&$key) {
                $key["code"] = substr($key["code"], 0, 1) . $key["restaurant_id"] . "00";
            });
        }
    }

    public function writeFile($filePath, $data = NULL) {
        $fileDetails = array();

        if ($data && $filePath) {
            $newFile = fopen($filePath, 'w+');
            fwrite($newFile, $data);
            fclose($newFile);
            chmod($filePath, 0777);
            $fileDetails = array('filepath' => $filePath,);
        }
        return $fileDetails;
    }
}
