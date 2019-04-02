<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $string      = $_POST['string'];
  $user_id 		= $_POST['user_id'];	
  $data = array(
        'table'=>'user_detail',
        'select'=>"users.id as id,user_detail.name as userName,profile_pic.profile_pic AS user_pic, 
        (
        	CASE WHEN 
        	(SELECT approved FROM friends_list WHERE (user_id=users.id OR friends_id=users.id) AND (user_id='".$user_id."' OR friends_id='".$user_id."') AND profile_type=1 AND approved=1 LIMIT 1)=1
        	THEN true 
        	ELSE false 
        	END 
        ) AS isFriend,
        (
        	CASE WHEN 
        	(SELECT approved FROM friends_list WHERE (user_id=users.id OR friends_id=users.id) AND (user_id='".$user_id."' OR friends_id='".$user_id."') AND profile_type=2 AND approved=1 LIMIT 1)=1
        	THEN true 
        	ELSE false 
        	END
        ) AS isFamily,
        (
        	CASE WHEN 
        	(SELECT approved FROM friends_list WHERE (user_id=users.id OR friends_id=users.id) AND (user_id='".$user_id."' OR friends_id='".$user_id."') AND profile_type=3 AND approved=1 LIMIT 1)=1
    	 	THEN TRUE 
    	 	ELSE false 
    	 	END
    	 ) 
    	 AS isProfessional ",
        'join'=>"JOIN users ON users.id=user_detail.user_id JOIN profile_pic ON user_detail.user_id = profile_pic.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=1 LIMIT 1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=2 LIMIT 1)=1 THEN 2 ELSE 3 END)",
        'where'=>"user_detail.name LIKE '".'%'.$string.'%'."' AND user_detail.user_id!='".$user_id."' AND users.deleted=0",
      );
  		$res = $db->get_all($data);
  		$mutimedia_dir = 'mutimedia_dir';
  		foreach ($res as $key => $value) {
  			if($value['isProfessional'] == 1)
  			{
  				$res[$key]['isProfessional'] = true;
  			}
  			else
  			{
  				$res[$key]['isProfessional'] = false;	
  			}
  			if($value['isFamily'] == 1)
  			{
  				$res[$key]['isFamily'] = true;
  			}
  			else
  			{
  				$res[$key]['isFamily'] = false;	
  			}
  			if($value['isFriend'] == 1)
  			{
  				$res[$key]['isFriend'] = true;
  			}
  			else
  			{
  				$res[$key]['isFriend'] = false;	
  			}
      //   COUNT(DISTINCT friends_list.user_id) 
        $data = array(
        'table'=>'friends_list',
        'select'=>"COUNT(DISTINCT friends_list.user_id) AS friends_count",
        'where'=>"user_id=".$value['id']." OR friends_id='".$value['id']."'",
      );
      $fr_co = $db->get_row($data);
      //print_r($fr_co);
      $res[$key]['friends_count'] = $fr_co['friends_count']; 
  			// if(!empty($value['userFriendPic']) || $value['userFriendPic'] != '')
  			// {
  			// 	$res[$key]['userFriendPic'] = $db->$mutimedia_dir.$value['userFriendPic'];
  			// }
  			// //echo $value['userFamilyPic'];
  			// if(!empty($value['userFamilyPic']) || $value['userFamilyPic'] != '')
  			// {
  			// 	$res[$key]['userFamilyPic'] = $db->$mutimedia_dir.$value['userFamilyPic'];
  				
  			// }
  			// if(!empty($value['userProPic']) || $value['userProPic'] != '')
  			// {
  			// 	$res[$key]['userProPic'] = $db->$mutimedia_dir.$value['userProPic'];
  				
  			// }  			
  		}
      $result = array(
        'status'=> true,
        'data'=>$res
      );
  echo json_encode($result);
}
?>