<?php

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', true);
require_once('Connect_class.php');
$db = new db_connect();
$email = $_POST['email'];
$country_code = $_POST['country_code'];
$mobile = $_POST['mobile'];
$password = $_POST['password'];
try {
    if (isset($_POST['email']) && !(empty($_POST['email'])) && $_POST['email'] != '') {
        $where = "`email`='" . $email . "'";
    } else {
        if(isset($_POST['mobile']) && $_POST['mobile'] != '')
        {
        $where = "`country_code`='" . $country_code . "' AND `mobile`='" . $mobile . "' ";
        }
        else
        {
            throw new Exception("Missing mobile nubmer");
            
        }
    }
    $data = array(
        'table' => 'users',
        'where' => $where,
    );
    $user = $db->get_row($data);
    if (isset($user) && count($user) > 0) {
        if (isset($_POST['password'])) {
            $data = array(
                'table' => 'users',
                'data' => " password='" . sha1($password) . "' ",
                'where' => " id='" . $user['id'] . "' "
            );
            $update = $db->update($data);
            $user_data = $db->get_user_data_id($user['id']);
            if ($update['status'] == true) {
                $user_data['email'] = $user['email'];
                $result = $user_data;
            } else {
                $result = array(
                    'status' => false,
                    'msg' => 'Something went wrong'
                );
            }
            echo json_encode($result);
            die();
        } else {
            throw new Exception("User or password is missing");
        }
    } else {
        throw new Exception("User not found");
    }
} catch (Exception $ex) {
    $result = array(
        'status' => false,
        'msg' => $ex->getMessage()
    );
    echo json_encode($result);
    die();
}
?>

