<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function createDynamicLink($link, $andoidPackageName = "", $IosPackageName = "") {
        $url = "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyBNpJvvJ8Qo_a0Uig1yTBOww9OIq-cCOK0";
        $ch = curl_init();    // initialize curl handle
                curl_setopt($ch, CURLOPT_URL, $url); // set POST target URL
                curl_setopt($ch, CURLOPT_POST, true); // set POST method
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $headers = [
                    'Content-Type: application/json',
                ];
        
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //Build the request for the session id. Make sure all payment field variables created above get included in the CURLOPT_POSTFIELDS section below.
                $dyLink = "https://Pegasus.page.link?link=".urlencode($link);
    
                if(!empty($andoidPackageName)) {
                    $dyLink .= "&apn=".$andoidPackageName;
                }
                if(!empty($IosPackageName)) {
                    $dyLink .= "&ibi=".$IosPackageName;
                }
                $data = array(
                    "longDynamicLink" => $dyLink,
                    "suffix" => array (
                        "option" => "SHORT"
                    )
                );
                curl_setopt(
                    $ch,
                    CURLOPT_POSTFIELDS,
                    json_encode($data)
                );
        
        
                $result = curl_exec($ch); // run the curl procss
                curl_close($ch); // Close cURL
                return $result;
    }

    public function hi($value='')
    {
        echo 1;
    }
}
