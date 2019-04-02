<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id1      = $_POST['user_id'];
  $profile_type = $_POST['profile_type'];
  $postid       = $_POST['post_id'];
  $limit        = 10;
  //print_r($_POST);exit;
  if($postid>0 and !empty($postid))
  {
    $id_limit =  'AND post.id < '.$postid.'';
  }
  else {
    $id_limit = '';
  }
  $friends = $db->friends_lists($user_id1,$profile_type);
  // print_r($friends);
  $data = array(
    'table'=>'post',
    'select'=>'post.id,post.user_id as user_id,post.post_type,post.method,user_detail.interest AS interest,user_detail.name AS username,profile_pic.profile_pic AS profile_pic,IF(post.post_type=4,(SELECT COUNT(id) FROM event_attand WHERE post_id=post.id),NULL) AS attanding,IF(post.post_type=4,(SELECT COUNT(id) FROM event_attand WHERE post_id=post.id AND user_id="'.$user_id1.'"),NULL) AS is_i_attanding,(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND liked=1) AS no_of_likes,(SELECT COUNT(*) FROM comment WHERE post_id=post.id and deleted=0) AS no_of_comment,(SELECT COUNT(*) FROM share_post WHERE post_id=post.id ) AS no_of_share,(CASE WHEN(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND user_id="'.$user_id1.'" AND liked=1 ) > 0 THEN true ELSE false END) AS isLikedByMe,(CASE WHEN post.status IS NULL THEN "" ELSE post.status END) AS status,(CASE WHEN post.images IS NULL THEN "" ELSE post.images END ) AS images,(CASE WHEN post.file IS NULL THEN "" ELSE post.file END ) AS file,(CASE WHEN post.video IS NULL THEN "" ELSE post.video END ) AS video,(CASE WHEN post.title IS NULL THEN "" ELSE post.title END ) AS title,(CASE WHEN post.event IS NULL THEN "" ELSE post.event END ) AS event ,(CASE WHEN post.lng IS NULL THEN "" ELSE post.lng END ) AS lng,(CASE WHEN post.lat IS NULL THEN "" ELSE post.lat END ) AS lat , post.date_time ',
    'join'=>'LEFT JOIN event ON event.id=post.event JOIN user_detail ON post.user_id=user_detail.user_id JOIN profile_pic ON post.user_id=profile_pic.user_id AND profile_pic.profile_type=post.profile_type',
    'where'=>'post.user_id IN ('.implode(',',$friends).') AND post.profile_type='.$profile_type.' AND post.activated=1 AND post.deleted=0 '.$id_limit.' ORDER BY post.id DESC LIMIT '.$limit
  );
  
  $result_data=$db->get_post_all($data);
  
  
  $user_id=$result_data[0]['user_id'];
  $list = $db->friends_list($user_id1);
  
   $query = "SELECT user_detail.user_id AS id,name,profile_pic.profile_pic AS user_pic,IF(
        ( SELECT approved FROM friends_list WHERE approved=1 AND (
            (user_id='".$user_id1."' AND friends_id=user_detail.user_id) OR
            (user_id=user_detail.user_id AND friends_id='".$user_id1."')
          ) AND profile_type=1 LIMIT 1
        ) =1,1,0
      )AS is_friend, IF(
        ( SELECT approved FROM friends_list WHERE approved=1 AND (
            (user_id='".$user_id1."' AND friends_id=user_detail.user_id) OR
            (user_id=user_detail.user_id AND friends_id='".$user_id1."')
          ) AND profile_type=2 LIMIT 1
        ) =1,1,0
      )AS is_faimily, IF(
        ( SELECT approved FROM friends_list WHERE approved=1 AND (
            (user_id='".$user_id1."' AND friends_id=user_detail.user_id) OR
            (user_id=user_detail.user_id AND friends_id='".$user_id1."')
          ) AND profile_type=3 LIMIT 1
        ) =1,1,0
      )AS is_professional FROM user_detail JOIN profile_pic ON user_detail.user_id = profile_pic.user_id AND profile_pic.profile_type=3 WHERE user_detail.user_id IN ('" . implode(',', $list) . "')";
      
	  $mysql = $db->conn->query($query);
	  $row = $mysql->fetch_assoc();
	  
	 $friend=$row['is_friend'];
	  $faimily= $row['is_faimily'];
	  $professional=$row['is_professional'];
	  if($friend==1)
	  $friend=1;
	  if($faimily==1)
	  $faimily=2;
	  if($professional==1)
	  $professional=3;
	  
	$interest_id=$result_data[0]['interest'];
	if(	$interest_id=='')
	{
	    
	  $interest_data = array(
  'table'=>'user_detail',
 'where' => " user_id in('" . $user_id1 . "')  ", 
 
 
); 
 
 $interest_result= $db->get_all( $interest_data);   
	}
	#####modified as the basis of some conflict variable in this api
	 if($interest_id=='')
	 $interest_id=$interest_result[0]['interest'];
	 
	 
	 $interest_id= str_replace(",", "','", $interest_id); 
	 $account_status=$friend.','.$faimily.','.$professional;
	 $account_status= str_replace(",", "','", $account_status); 
 
	  
	  
#######################feed code start####################################
 		 
 $feed_data = array(
  'table'=>'account_tbl',
 'where' => " category in('" . $interest_id . "') or `account_status` in('" . $account_status . "')  ", 
 
 
); 
 
 $feed_result= $db->get_all( $feed_data);
 
  $mutimedia_image_dir = 'https://' . $_SERVER['SERVER_NAME'] . '/new_admin/viaspot_users/images/';
$mutimedia_video_dir = 'https://' . $_SERVER['SERVER_NAME'] . '/new_admin/viaspot_users/';
$mutimedia_userimage_dir = 'https://' . $_SERVER['SERVER_NAME'] . '/new_admin/viaspot_users/user_images/';

 
foreach ($feed_result as $feed_row)
{
    if($feed_row['status']==1)
    {
     $user_id=$feed_row['user_id'];
      
    
	 $user_profile_data = array(
  'table'=>' admin_user',
 'where' => " id= '" . $user_id . "'  ",
);
    $user_result= $db->get_row( $user_profile_data);
	  $img = ($feed_row['image'] != '')?$mutimedia_image_dir.$feed_row['image']:'';
$video = ($feed_row['video'] != '')?$mutimedia_video_dir.$feed_row['video']:'';
$user_img= (!empty($user_result ['profile_image']) && $user_result ['profile_image'] != '')?$mutimedia_userimage_dir .$user_result ['profile_image']:'';
 
 
   if($feed_row['image'] !='' )
 $post_type='2';
  else if($feed_row['video'] != '' )
 $post_type='3';
  else if($feed_row['url'] != '' )
 $post_type='7';
 else
 $post_type='1';
 
 if($feed_row['url'] != '')
 $video =$feed_row['url'];
 
 
$image=array();
if($img!='')
 
  $image[] = array('image_data' => $img);
else 
 
	$image =array();
	
 
 
####Condition Check##########
if($user_result['username']=='admin')
$username='viaspot';
else 
$username=$user_result['username'];
####Condition Check##########

$final_feed_result[]=array("id"=>$feed_row['id'],"user_id"=>$feed_row['user_id'],"post_type"=>$post_type,"method"=>'feed',"interest"=>$feed_row['category'],"username"=> $username,"profile_pic"=>$user_img,"attanding"=>null,"is_i_attanding"=>null,"no_of_likes"=>"0","no_of_comment"=>"0","no_of_share"=>"0","isLikedByMe"=>false,"status"=>$feed_row['description'],"images"=>$image,"file"=>"","video"=>$video,"title"=>$feed_row['account_name'],"event"=>"","lng"=>"","lat"=>"","date_time"=>$feed_row['date']);  
}
}
 
 $result_data=array_merge($result_data,$final_feed_result);
 
 ###############sort array##############
 function invenDescSort($item1,$item2)
{
    if ($item1['date_time'] == $item2['date_time']) return 0;
    return ($item1['date_time'] < $item2['date_time']) ? 1 : -1;
}
usort($result_data,'invenDescSort');
 ##############sort array###############
 ##############################feed code end################################
 if($result_data=='')
 $result = array(
    'status'=> true,
    'data'=>array()
  );
 
 else
 
 
  $result = array(
    'status'=> true,
    'data'=>$result_data 
  );
 
 /* $result = array(
    'status'=> true,
    'data'=>$result_data,
	'feed_data'=>$final_feed_result
  );*/
  echo json_encode($result);
}
?>
