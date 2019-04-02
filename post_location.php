<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $user_id = $_POST['user_id'];
    $profile = $_POST['profile_type'];
    $lat = $_POST['lattitude'];
    $long = $_POST['longitute'];
    $status = addslashes($_POST['status']);
    $post_type = 5;
    $activated = 1;
    try {
        if (isset($_POST['user_id']) && isset($_POST['profile_type']) && isset($_POST['lattitude']) && isset($_POST['longitute'])) {
            $pro = explode(",", $profile);
            foreach ($pro as $key => $value) {
                $data = array(
                    'table' => 'post',
                    'field' => 'user_id,profile_type,post_type,lng,lat,status,activated',
                    'values' => array(array($user_id, $value, $post_type,$long,$lat, $status, $activated))
                );
                $result = $db->insert($data);
                if ($result['status'] == true) {
                    $db->post_notifiation($_POST['user_id'],$value,$result['inserted_id'],$post_type);
                } else {
                    throw new Exception("post insert query failed : ".$db->conn->error());
                }
                echo json_encode($result);
            }
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
