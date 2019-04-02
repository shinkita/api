<?php

//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
try {
    require_once('Connect_class.php');
    $db = new db_connect();
    if ($IpAddr = $db->getRealIpAddr()) {
        if ($IpDetail = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $IpAddr)) {
            $IpDetail = json_decode($IpDetail);             
            if (!empty($IpDetail->geoplugin_countryName)) {
                $query = "SELECT phonecode FROM country WHERE name LIKE '" . $IpDetail->geoplugin_countryName."' ";
                if ($result = $db->conn->query($query)) {
                    $phone_code = $result->fetch_assoc();                    
                    echo json_encode(array(
                        'status' => true,
                        'msg' => 'phone code found',
                        'country_code'=>$phone_code['phonecode']
                    )); 
                    die();
                } else {
                    throw new Exception("Phone code query failed");
                }
            } else {
                throw new Exception('Country not found');
            }
        } else {
            throw new Exception("Geoplugin failed");
        }
    } else {
        throw new Exception("Fetching Ip address failed");
    }
} catch (Exception $ex) {
    echo json_encode(array(
        'status' => false,
        'msg' => $ex->getMessage()
    ));
}
?> 