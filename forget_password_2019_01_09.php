<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $email = $_POST['email'];
    $country_code = preg_replace("/[^0-9]/", "", $_POST['country_code']);
    $mobile = preg_replace("/[^0-9]/", "", $_POST['mobile']);
    $otp = $_POST['otp'];    
    try {
        if (!(isset($_POST['otp'])) || empty($_POST['otp']) || $_POST['otp'] == '') {
            throw new Exception("Missing otp");
            die();
        }
        if (isset($_POST['email']) && !(empty($_POST['email'])) && $_POST['email'] != '') {
            $where = "`email`='" . $email."'";
        } else {
            $where = "`country_code`='" . $country_code . "' AND `mobile`='" . $mobile . "'";
        }
        $data = array(
            'table' => 'users',
            'where' => $where,
        );
        $user = $db->get_row($data);
        if (empty($user) || count($user) == 0) {
            throw new Exception("User does not exist !");
        } else if ($user['activated'] == 1) {
            $result = array();
            if (isset($_POST['email']) && !(empty($_POST['email'])) && $_POST['email'] != '') {
                $email = array(
                    'subject' => 'otp',
                    "body" => 'Your otp is ' . $otp,
                    "altbody" => 'Your otp is ' . $otp
                );
                $db->send_mail($user['email'], $email); 
                $result = array(
                    'status' => true,                  
                    'msg' => 'otp sent',                
                );
            }
            else{
                if ($user['country_code'] == 91 || $user['country_code'] == '+91' || $user['country_code'] == '091') {
                    $msg = urlencode("Your OTP is " . $otp);
                    $db->send_msg_national($user['country_code'] . $user['mobile'], $msg);
                } else {
                    $msg = urlencode("Your OTP is " . $otp);
                    $db->send_msg_international($user['country_code'] , $user['mobile'], $msg);
                    $result = array(
                        'status' => true,                  
                        'msg' => 'otp sent',                
                    );
                }
            }
            echo json_encode($result);
            die();
        } else {
            throw new Exception("User Not Activated !");
        }
    } catch (Exception $ex) {
        $result = array(
            'status' => false,
            'msg' => $ex->getMessage(),            
        );
        echo json_encode($result);
        die();
    }
}
?>