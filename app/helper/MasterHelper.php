<?php

namespace App\helper;

use Config;
use Illuminate\Support\Str;
use App\Models\TblLocation;
use App\Models\TblState;
use App\Models\TblHomeType;
use App\Models\TblAmenities;
use App\Models\TblHomeAmenities;
use App\Models\TblTag;
use App\Models\TblIcon;
use App\Models\RuLog;
use DB;



class MasterHelper
{
    public static function getLocationList($state_id = '')
    {
        if ($state_id) {
            return TblLocation::where('status', 1)->where('state_id', $state_id)->get();
        }
    }

    public static function getStateList()
    {
        return TblState::where('status', 1)->get()->toArray();
    }

    public static function getHomeType()
    {
        return TblHomeType::where('status', 1)->get()->toArray();
    }

    public static function getAmenities()
    {
        return TblAmenities::select(
            'tbl_amenities.*'
        )->where('status', 1)->get()->toArray();
    }

    public static function getTag()
    {
        return TblTag::select(
            'tbl_tags.*'
        )->where('status', 1)->get()->toArray();
    }

    public static function getIcons()
    {
        return TblIcon::where('status', 1)
            ->orderBy('icons_name')
            ->get()
            ->toArray();
    }

    public static function checkedAmenities($amenities_id, $home_id)
    {
        return TblHomeAmenities::where(['home_id' => $home_id, 'amenities_id' => $amenities_id])->get();
    }



    public static function makeXmlRequest($xml)
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-type: application/x-www-form-urlencoded; charset=utf-8'
            ));
            curl_setopt($curl, CURLOPT_URL, config('ru.RU_URL'));
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
            if ($xml != "") {
                if (is_array($xml)) {
                    $xml = implode("&", $xml);
                }
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
            }

            $xml_result = curl_exec($curl);
            $result = simplexml_load_string($xml_result);

            curl_close($curl);
            $result_json = json_encode($result);
            $result_array = json_decode($result_json, TRUE);
            Self::saveRuLog($xml, $result_array);
            $data = array('success' => true, 'message' => 'Listed successfully', 'code' => 200, 'data' => $result_array);
        } catch (Exception $e) {
            $data = array('success' => false, 'message' => $e->getmessge(), 'code' => 500, 'data' => null);
        }
        return $data;
    }



    public static function getDateDifference($date1, $date2)
    {
        $date1_strtotime = strtotime($date1);
        $date2_strtotime  = strtotime($date2);
        $datediff = $date2_strtotime - $date1_strtotime;
        return floor($datediff / (60 * 60 * 24));
    }



    public static function saveRuLog($api_request, $response)
    {
        $api_status = false;
        $xml_req_log = str_replace(config('ru.RU_PASSWORD'), 'xxxxxxxxxxxxxx', str_replace(config('ru.RU_USER_NAME'), 'xxxxxxxxxxxxxx', $api_request));
        $log = array();
        if (isset($response['Status']) && $response['Status'] == 'Success') {
            $api_status = true;
        }

        $log['api_request'] = $xml_req_log;
        $log['response_id'] = isset($response['Status']) ? $response['ResponseID'] : 'server' . time();
        $log['message'] = isset($response['Status']) ? $response['Status'] : 'Error';
        $log['api_status'] = $api_status;
        $log['log'] = json_encode($response, true);
        $log['add_ip'] = request()->ip();

        try {
            return RuLog::create($log);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
