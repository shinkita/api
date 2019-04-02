<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
    require_once('Connect_class.php');
    $db =  new db_connect();
    $user_id      = $_POST['user_id'];
    $group_id      = $_POST['group_id'];
    $msg          = addslashes($_POST['msg']);
    $chat_type = $_POST['chat_type'];
    $video = $_POST['video'];
    $document = $_POST['document'];
    $lat = $_POST['lattitude'];
    $long = $_POST['longitude'];
    $thumbnail = $_POST['thumbnail'];
    $img    = $_POST['img'];
    $title = addslashes($_POST['title']);
    $reply_to_msg = '-1';
    $data = array(
            'table'=>'group_chat',
            'field'=>'user_id,group_id,message,img,video,thumbnail,document,title,lng,lat,chat_type',
            'values'=>array(array($user_id,$group_id,$msg,$img,$video,$thumbnail,$document,$title,$long,$lat,$chat_type))
        );
    $result = $db->insert($data); 
    if($result['status'] == true)
    {
        $data = array(
            'table'=>"group_users",
            'data'=>"`readed`=(`readed`+1) ",
            'where'=>"`group_id`='".$group_id."'",
        );
        $db->update($data);
        $query = "SELECT user_id FROM group_users WHERE group_id='".$_POST['group_id']."' AND user_id!='".$_POST['user_id']."'";
        if(!($stmt = $db->conn->prepare($query)))
        {
            throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
        }     
        if(!$stmt->execute())
        {
            throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
        }
        $data = $stmt->get_result();        
        while($row = $data->fetch_assoc())
        {
            // print_r($row);
            $user_data = array(
                'table'=>'user_detail',
                'select'=>'user_detail.user_id AS user_id,user_detail.name AS user_name,profile_pic.profile_pic AS user_pic',
                'join'=>" JOIN profile_pic ON profile_pic.user_id=user_detail.user_id AND profile_pic.profile_type=3 JOIN profile_type ON profile_type.id=profile_pic.profile_type ",
                'where'=>"user_detail.user_id='".$row['user_id']."' ",
            );
     //print_r($user_data);
            $ggroup = $db->get_row(array(
                'table'=>'chat_groups',
                'select'=>' id,name,pic',
                'where'=>"id='".$group_id."'"
                )
            );
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
                $notify_type = 'group_chat_message';
                $fields = array (
                    'to'=>"/topics/via".$user['user_id'],
                    'data' => array (
                      'id'=>$result['inserted_id'],
                      "noti_type"=>$notify_type,
                      'group_id'=>$group_id,
                      'replytomsg_id'=>$reply_to_msg,
                        "group_name"=>$ggroup['name'],
                        "group_pic"=>$db->$multimedia_dir.$ggroup['pic'],
                        "user_id"=>$current_user['id'],
                        "user_name"=>$current_user['name'],
                        "user_pic"=>$current_user['user_pic'],
                      'message'=>$_POST['msg'],
                        'img'=>$img,
                        'video'=>$video,
                        'thumbnail'=>$thumbnail,
                        'lat'=>$lat,
                        'lng'=>$long,
                        'document'=>$document,
                        'chat_type'=>$chat_type,
                        'title'=>$_POST['title'],
                      'date'=>$result['date_time']  
                    )
                 );
                // print_r($fields);
                $stat = $db->sendPushNotification($fields);
        }
    }
}
echo json_encode($result);
?>