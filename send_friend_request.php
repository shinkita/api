<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once ('Connect_class.php');
    $db = new db_connect();
    $user_id = $_POST['user_id'];
    $friend_id = $_POST['to_user_id'];
    $profile_type = $_POST['profile_type'];
    try {
        if (isset($_POST['user_id']) && isset($_POST['to_user_id']) && isset($_POST['profile_type'])) {
            $find_request = array(
                'table' => 'friends_list',
                'where' => ' user_id="' . $user_id . '" AND friends_id="' . $friend_id . '" AND profile_type="' . $profile_type . '"'
            );
            $find_count = $db->count($find_request);            
            if ($find_count > 0) {

                throw new Exception('Request already pending');
                die();
            } else {
                $data = array(
                    'table' => 'friends_list',
                    'field' => 'user_id,profile_type,approved,friends_id',
                    'values' => array(
                        array(
                            $user_id,
                            $profile_type,
                            0,
                            $friend_id
                        )
                    )
                );
                
                $result1 = $db->insert($data);
                // $result1['status'] = true;
                if ($result1['status'] == true) {
                    $data = array(
                        'table' => 'users',
                        'select' => ' users.id AS user_id,user_detail.name AS username,profile_type.id AS profile_id,profile_type.profile AS profile_type,profile_pic.profile_pic AS userimage',
                        'join' => ' JOIN user_detail ON users.id=user_detail.user_id JOIN profile_pic ON profile_pic.user_id=users.id JOIN profile_type ON profile_type.id=profile_pic.profile_type ',
                        'where' => "users.id='" . $user_id . "' AND profile_pic.profile_type='$profile_type'"
                    );
                    $result = $db->get_row($data);
                    // print_r($result);
                    $multimedia_dir = 'mutimedia_dir';
                    if (isset($result['userimage']) && ! empty($result['userimage'])) {
                        $result['userimage'] = $db->$multimedia_dir . $result['userimage'];
                    }
                    $notify_type = 'friend_request_receive';
                    $noti_data = array(
                        'table' => 'notification',
                        'field' => 'user_id,to_user_id,profile_type,notify_type',
                        'values' => array(
                            array(
                                $user_id,
                                $friend_id,
                                $profile_type,
                                $notify_type
                            )
                        )
                    );
                    // print_r($noti_data);
                    $res = $db->insert($noti_data);
                    // print_r($res);
                    $fields = array(
                        'to' => "/topics/via" . $friend_id,
                        'data' => array(
                            'id' => $res['inserted_id'],
                            "noti_type" => $notify_type,
                            "user_id" => $result['user_id'],
                            "user_name" => $result['username'],
                            "profile_type_id" => $result['profile_id'],
                            "profile_type" => $result['profile_type'],
                            'user_pic' => $result['userimage']
                        )
                    );
                    $stat = $db->sendPushNotification($fields);
                    $status = array(
                        'status' => true,
                        'msg' => 'Request sent'
                    );
                    echo json_encode($status);
                    die();
                    // $fields = json_encode($fields);
                }
                else
                {
                    throw new Exception('Database insert query failed');
                }
            }
        } else {
            throw new Exception('Required field is missing');
        }
    } catch (Exception $e) {
        $result = array(
            'status' => false,
            'msg' => $e->getMessage()
        );
        echo json_encode($result);
    }
}
?>
