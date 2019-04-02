<?php

// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $email = $_POST['email'];
    $country_code = preg_replace("/[^0-9]/", "", $_POST['country_code']);
    $mobile = preg_replace("/[^0-9]/", "", $_POST['mobile']);
    $otp = $_POST['otp'];
	$key = $_POST['key'];
    try {
        if (isset($_POST['otp']) && isset($_POST['email']) && isset($_POST['key'])) {
            if(isset($_POST['country_code']) && isset($_POST['mobile']) && $_POST['mobile'] != '')
            {
                if ($country_code == 91 || $country_code == '+91' || $country_code == '091' || $country_code == '91') {
                    /* $msg = urlencode("Your OTP is " . $otp); */
                    if($key==1)
                    $msg = urlencode("<#> Your viaspot code is: " . $otp.'');
                    else
					$msg = urlencode("<#> Your viaspot code is: " . $otp.' '.$key);
					
                    $db->send_msg_national($country_code .$mobile, $msg);
                } else {
                   if($key==1)
                    $msg = urlencode("<#> Your viaspot code is: " . $otp.'');
                    else
				    $msg = urlencode("<#> Your viaspot code is: " . $otp.' '.$key);
                    $db->send_msg_international($country_code,$mobile, $msg);
                }
            }
            
            
            $otplogs = "logs/otp_logs_".date('Ymd').".txt";
	$otpdata=$otp.'|'.$key.'|'.$email;
	error_log($otpdata, 3,  $otplogs);
	        if($key==1)
	        	$email_msg = array(
                'subject' => 'otp',
                "body" => "<#> Your viaspot code is: " . $otp.'',
                "altbody" => "<#> Your viaspot code is: " . $otp.''
            );
	        else
			$email_msg = array(
                'subject' => 'otp',
                "body" => "<#> Your viaspot code is: " . $otp.' '.$key,
                "altbody" => "<#> Your viaspot code is: " . $otp.' '.$key
            );
            $db->send_mail($email, $email_msg);
            $result = array(
                'status' => true,
                'msg' => 'otp sent'
            );
            echo json_encode($result);
            die();
        }
        else
        {
            throw new Exception('Any post field is missing');
        }
    } catch (Exception $ex) {
        $result = array(
            'status' => false,
            'msg' => $ex->getMessage()
        );
        echo json_encode($result);
        die();
    }
}
?>