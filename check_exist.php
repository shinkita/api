<?php

//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    try {
        if(isset($_POST['email']))
        {
          $field = 'email';
          $value = $_POST['email'];
        }
        elseif(isset($_POST['mobile']))
        {
          $field = 'mobile';
          $value = $_POST['mobile'];
        }
        else{
          throw new Exception('missing required field');
        }
        
            $data = array(
                'table' => 'users',
                'where' => " ".$field."='" . $value . "' "
            );
            $user = $db->get_row($data);
            //print_r($user);
            if (!empty($user)) {
                $result = array(
                    'status'=>false,
                    'msg'=>'User already Exist'
                );
                echo json_encode($result);
                die();
              }
              else
              {
                throw new Exception("user not exist");
                
              }
    } catch (Exception $ex) {
        $result = array(
            'status' => true,            
            'msg' => $ex->getMessage()
        );
        echo json_encode($result);
        die();
    }
}
?>