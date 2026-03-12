<?php

namespace App\Http\Controllers\PMS\Property;

use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
use App\Models\TblHomeUnit;

class ExternalChannelController extends Controller{
    
    public function index(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://webapi.rentalsunited.com/whitepms/oauth2/token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'grant_type=password&username='.config('ru.CHANNEL_MANAGER_USERNAME').'&password='.config('ru.CHANNEL_MANAGER_PASSWORD'),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
          ),
        ));
        $response = curl_exec($curl);
        $resArray = json_decode($response, true);
        
        if(!isset($resArray['access_token'])){
           abort(401, 'Incorrect Login or Password');
        }
      
        $token = $resArray['access_token'];
        $refreshToken = '';
        $client = new Client();
        $headers = [
          'Authorization' => 'Bearer '.$token,
        ];
        $request = new Request('GET', 'https://webapi.rentalsunited.com/api/white-pms/client?userName='.config('ru.RU_USER_NAME'), $headers);
        $res = $client->sendAsync($request)->wait();
        $result = $res->getBody()->getContents();

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();

        @$dom->loadHTML($result);

        $hostElement = $dom->getElementsByTagName('white-pms-host')->item(0);
        $attributes = array();
        if ($hostElement) {
            $attributes = [];
            foreach ($hostElement->attributes as $attr) {
                $cleanedValue = str_replace(['\\"', '"'], '', trim($attr->value, '"'));
                $attributes[$attr->name] = $cleanedValue;
            }
            $token =  rtrim($attributes['token'], '\\');
            $refreshToken =  rtrim($attributes['refresh_token'], '\\');
        }
        else {
            echo "No <white-pms-host> tag found!";
        }
        
        $src = "https://new.rentalsunited.com/white-pms-client/script?token=".$token."&refreshToken=".$refreshToken."&languageId=1";
        $html = '<div id="ruApp" ><script src="' . $src . '"></script></div>';
        $t = '';
        return view('pms.property.external-channel', compact('token','refreshToken', 'html', 't'));
    }
    
    
    public function channelManagerValidationForm(){
        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        
        $xml = "<Pull_GetAgents_RQ>
                 <Authentication>
                   <UserName>".config("ru.RU_USER_NAME")."</UserName>
                   <Password>".config("ru.RU_PASSWORD")."</Password>
                 </Authentication>
        </Pull_GetAgents_RQ>";
        
        $request = new Request('POST', 'https://rm.rentalsunited.com/api/Handler.ashx', $headers, $xml);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);
        
        $channelManagerArray = array();
        if(isset($result_array['Agents']['Agent'])){
            foreach($result_array['Agents']['Agent'] as $agent){
                $name = $agent['CompanyName'];
                if(!is_array($name)){
                    $channelManagerArray[] = array(
                        'AgentID'=>$agent['AgentID'],
                        'Name'=>$name
                    );
                }
            }
        }
        usort($channelManagerArray, function ($a, $b) {
            return strcmp($a['Name'], $b['Name']);
        });
        $properties = TblHomeUnit::whereNotNull('ru_property_id')->get();
        return view('pms.property.channel-manager-validation-form', compact('channelManagerArray', 'properties'));
    }
}