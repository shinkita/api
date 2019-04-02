<?php

//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
try {
    require_once('Connect_class.php');
    $db = new db_connect();

    $result = $db->get_user_data_id('37');
    echo json_encode($result);
} catch (Exception $ex) {
    echo json_encode(array(
        'status' => false,
        'msg' => $ex->getMessage()
    ));
}
?>
