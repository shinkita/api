<?php

//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $email = $_POST['email'];
    $country_code = $_POST['country_code'];
    $mobile = $_POST['mobile'];
    try {
        if (isset($_POST['email'])) {
            if(isset($_POST['mobile']) && $_POST['mobile'] != '')
            {
                $data = array(
                    'table' => 'users',
                    'where' => "`email`='" . $email . "' OR (country_code='" . $country_code . "' AND mobile='" . $mobile . "')"
                );
            }
            else
            {
                $data = array(
                    'table' => 'users',
                    'where' => "`email`='" . $email . "'"
                );
            }
            $user = $db->get_row($data);
            //print_r($user);
            if (!empty($user) && $user['deleted'] == 0) {
                if ($user['activated'] == 1) {
                    $activate = true;
                } else {
                    $activate = false;
                }
                $multimedia_dir = 'mutimedia_dir';
                $data_user = array(
                    'table' => 'user_detail',
                    'select' => 'user_detail.user_id AS id,user_detail.name AS name,(SELECT profile_pic FROM profile_pic WHERE profile_type=1 AND user_id=' . $user['id'] . ') AS friend_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=2 AND user_id=' . $user['id'] . ') AS faimily_pic,(SELECT profile_pic FROM profile_pic WHERE profile_type=3 AND user_id=' . $user['id'] . ') AS pro_pic',
                    'where' => 'id=' . $user['id'] . ''
                );
                //     print_r($data_user);exit;
                $u_detail = $db->get_row($data_user);
                //  print_r($u_detail);
                if ($u_detail['friend_pic'] != '' && !empty($u_detail['friend_pic'])) {
                    $u_detail['friend_pic'] = $db->$multimedia_dir . $u_detail['friend_pic'];
                }
                if ($u_detail['faimily_pic'] != '' && !empty($u_detail['faimily_pic'])) {
                    $u_detail['faimily_pic'] = $db->$multimedia_dir . $u_detail['faimily_pic'];
                }
                if ($u_detail['pro_pic'] != '' && !empty($u_detail['pro_pic'])) {
                    $u_detail['pro_pic'] = $db->$multimedia_dir . $u_detail['pro_pic'];
                }
                $result = array(
                    'user_exist' => true,
                    'user_activated' => $activate,
                    'id' => $user['id'],
                    'name' => $u_detail['name'],
                    'friend_pic' => $u_detail['friend_pic'],
                    'faimily_pic' => $u_detail['faimily_pic'],
                    'pro_pic' => $u_detail['pro_pic'],
                );
                echo json_encode($result);
                die();
            } else {
                throw new Exception('User deleted Or does not exist');
            }
        } else {
            throw new Exception('Email or phone number is missing');
        }
    } catch (Exception $ex) {
        $result = array(
            'user_exist' => false,
            'user_activated' => false,
            'msg' => $ex->getMessage()
        );
        echo json_encode($result);
        die();
    }
}
?>