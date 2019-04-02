<?php

// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $p_pic_fr = $_POST['pic_friend'];
    $p_pic_family = $_POST['pic_family'];
    $p_pic_pro = $_POST['pic_pro'];
    $mobile = $_POST['mobile'];
    $path = $db->mutimedia_dir;
    $country_code = $_POST['country_code'];
    try {
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['dob']) && isset($_POST['gender']) && isset($_POST['pic_friend']) && isset($_POST['pic_family']) && isset($_POST['pic_pro']) && isset($_POST['mobile']) && isset($_POST['country_code'])) {            
            $data = array(
                'table' => 'users',
                'field' => 'email,password,activated,mobile,country_code',
                'values' => array(array($email, sha1($password), 1, $mobile,$country_code))
            );
            $result = $db->insert($data);
            function upload_pic($profile_pic, $path, $user_id) {
                $image_name = time() . '' . $user_id . '' . rand() . '.png';
                $encode = file_put_contents($path . '' . $image_name, base64_decode($profile_pic));
                if ($encode == true) {
                    return $image_name;
                } else {
                    return '';
                }
            }            
            $friend_pic = upload_pic($p_pic_fr, $path, $result['inserted_id']);
            $family_pic = upload_pic($p_pic_family, $path, $result['inserted_id']);
            $pro_pic = upload_pic($p_pic_pro, $path, $result['inserted_id']);
            $shadow = array(
                'table' => 'profile_pic',
                'field' => 'user_id,profile_pic,profile_type',
                'values' => array(array($result['inserted_id'], $friend_pic, 1), array($result['inserted_id'], $family_pic, 2), array($result['inserted_id'], $pro_pic, 3))
            );
            $shadow1 = $db->insert(array('table' => 'user_detail', 'field' => 'user_id,name,dob,gender', 'values' => array(array($result['inserted_id'], $name, $dob, $gender))));
            $shadow_result = $db->insert($shadow);
            $shadow3 = array(
                'table' => 'user_profile_about',
                'field' => 'user_id,profile_type,bio,phone,address,website,education,work,description,previous_work',
                'values' => array(array($result['inserted_id'], 1, '', '', '', '', '', '', '', ''), array($result['inserted_id'], 2, '', '', '', '', '', '', '', ''), array($result['inserted_id'], 3, '', '', '', '', '', '', '', ''))
            );
            $shadow3 = $db->insert($shadow3);
            if ($user['country_code'] == 91 || $user['country_code'] == '+91' || $user['country_code'] == '091') {
                $msg = urlencode("Your OTP is " . $otp);
                $db->send_msg_national($user['country_code'] . $user['mobile'], $msg);
            } else {
                $msg = urlencode("Your OTP is " . $otp);
                $db->send_msg_international($user['country_code'] . $user['mobile'], $msg);
            }
            $email = array(
                'subject' => 'otp',
                "body" => 'Your OTP is ' . $otp,
                "altbody" => 'Your OTP is ' . $otp
            );
            $db->send_mail($user['email'], $email);
            $result = array(
                'status' => true,
                'msg' => 'otp sent',
                'data' => $_POST
            );
            echo json_encode($result);
            die();
        } else {
            throw new Exception('Any required field missing');
        }
    } catch (Exception $e) {
        $result = array(
            'status' => false,
            'msg' => $e->getMessage()
        );
    }
}
?>


