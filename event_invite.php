<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
    require_once('Connect_class.php');
    $db = new db_connect();
    $user_id      = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    $invite_list = $_POST['list'];
    $list = json_decode( $invite_list);    
    foreach ($list as $key => $value) {
            $user_data = array(
                'table'=>'user_detail',
                'select'=>'user_detail.id AS user_id,user_detail.name AS user_name,profile_pic.profile_pic AS user_pic',
                'join'=>" JOIN profile_pic ON profile_pic.user_id=user_detail.user_id AND profile_pic.profile_type=3 JOIN profile_type ON profile_type.id=profile_pic.profile_type ",
                'where'=>"user_detail.user_id='".$value['user_id']."' ",
            );
     //print_r($user_data);
            $current_user = $db->get_row(
                array(
                    'table'=>'users',
                    'select'=>" users.id AS user_id,user_detail.name AS name ,IF(profile_pic.profile_pic!='',CONCAT('".$db->mutimedia_dir."',profile_pic.profile_pic),'') AS user_pic",
                    'join'=>' JOIN user_detail ON user_detail.user_id=users.id JOIN profile_pic ON profile_pic.user_id=users.id AND profile_pic.profile_type=3',
                    'where'=>"users.id='".$_POST['user_id']."'"
                )
            );
            $user = $db->get_row($user_data);
                $multimedia_dir = 'mutimedia_dir';
                if(!empty($user['user_pic']))
                { 
                    //echo  'this working';
                    $user['user_pic'] = $db->$multimedia_dir.$user['user_pic'];
                }        
                $notify_type = 'event_invite';
                $fields = array (
                    'to'=>"/topics/via".$user['user_id'],
                    'data' => array (
                      'id'=>$result['inserted_id'],
                      "noti_type"=>$notify_type,
                      'post_id'=>$post_id,
                        "user_id"=>$current_user['id'],
                        "user_name"=>$current_user['name'],
                        "user_pic"=>$current_user['user_pic'],
                    )
                 );
                //print_r($fields);
                $stat = $db->sendPushNotification($fields);
        }
        echo json_encode(array('status' => true,'msg'=>"invitation sended" ));
}

?>