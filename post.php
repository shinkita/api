<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {    
    require_once('Connect_class.php');
    $db = new db_connect();
    $user_id = $_POST['user_id'];
    $profile = $_POST['profile_type'];
    // $status = $_POST['status'];
    $status = addslashes($_POST['status']);
    // echo $status;
    $post_type = 1;
    $activated = 1;    
    try {
        if (isset($_POST['user_id']) && isset($_POST['profile_type']) && isset($_POST['status'])) {
            $result = array();
            $pro = explode(",", $profile);
            $date = date('Y-m-d H:i:s');
            foreach ($pro as $key => $value) {
                $data = array(
                'table' => 'post',
                'field' => 'user_id,profile_type,post_type,status,activated,update_date',
                'values' => array(array($user_id, $value, $post_type, $status, $activated, $date))
                );
                $result = $db->insert($data);    
                if ($result['status'] == true) {
                    $db->post_notifiation($_POST['user_id'],$value,$result['inserted_id'],$post_type);
                }
                else
                {
                    throw new Exception("status post query failed");
                }
            }
            echo json_encode($result);
            
        } else {
            throw new Exception("required field is missing");
        }
    } catch (Exception $ex) {
        $result = array(
            'status' => false,
            'msg' => $ex->getMessage()
        );
        echo json_encode($result);
    }
}
?>
