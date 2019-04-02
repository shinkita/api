<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
    require_once('Connect_class.php');
    $db =  new db_connect();
    $user_id   = $_POST['user_id'];
	$replyto_msg_id  = $_POST['msg_id'];
    $to_user      = $_POST['to_user_id'];
    $msg          = addslashes($_POST['msg']);
    $chat_type = 'msg';
    $data = array(
            'table'=>'chat',
            'field'=>'user_id,to_user_id,replyto_msg_id,message,chat_type',
            'values'=>array(array($user_id,$to_user,$replyto_msg_id,$msg,$chat_type))
        );
    $result = $db->insert($data); 
    if($result['status'] == true)
    {
        $user_data = array(
                    'table'=>'user_detail',
                    'select'=>' user_detail.id AS user_id,user_detail.name AS user_name,profile_pic.profile_pic AS user_pic',
                    'join'=>" JOIN profile_pic ON profile_pic.user_id=user_detail.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id ='".$user_id."' OR friends_id = '".$user_id."') AND (user_id = '".$to_user."' OR friends_id = '".$to_user."') AND approved=1 AND profile_type=1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '".$user_id."' OR friends_id = '".$user_id."') AND (user_id = '".$to_user."' OR friends_id = '".$to_user."') AND approved=1 AND profile_type=2)=1 THEN 2 ELSE 3 END)  JOIN profile_type ON profile_type.id=profile_pic.profile_type ",
                    'where'=>"user_detail.user_id='".$user_id."' ",
                );
        $user = $db->get_row($user_data);
        $multimedia_dir = 'mutimedia_dir';
        if(!empty($user['user_pic']))
        {             
            $user['user_pic'] = $db->$multimedia_dir.$user['user_pic'];
        }        
        $notify_type = 'chat_message';
        $fields = array (
				'to'=>"/topics/via".$to_user,
				'data' => array (
				'id'=>$result['inserted_id'],
				"noti_type"=>$notify_type,
				"user_id"=>$user['user_id'],
				"user_name"=>$user['user_name'],
				'user_pic'=>$user['user_pic'],
				'reply_msg_id'=>$replyto_msg_id,
				'message'=>$_POST['msg'],
				'chat_type'=>$chat_type,
				'date'=>$result['date_time']  
            )
         );
        $stat = $db->sendPushNotification($fields);
    }
}
echo json_encode($result);
?>
