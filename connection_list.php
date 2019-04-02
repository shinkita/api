<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
	require_once('Connect_class.php');
	$db = new db_connect();
	$user_id    = $_POST['user_id'];  
	$friends = $db->friends_list_datail_chat($user_id);
	$i=0;
	foreach($friends as $val){
		$uid = $val['id'];
		$sql = "SELECT * FROM friends_list WHERE approved=1 AND user_id IN (".$uid.",".$user_id.") AND friends_id IN (".$uid.",".$user_id.")";
		$msql = $db->conn->query($sql);
		if ($msql->num_rows > 0) {
			while ($row = $msql->fetch_assoc()) {
					if($row['profile_type'] == 1)
					$friends[$i]['isFriend']='1';
					if($row['profile_type'] == 2)
					$friends[$i]['isFamily']='1';
					if($row['profile_type'] == 3)
					$friends[$i]['isProfessional']='1';				   
			}
			if (!isset($friends[$i]['isFamily']))
			{
				$friends[$i]['isFamily'] = 0;
			}
			if (!isset($friends[$i]['isProfessional']))
			{
				$friends[$i]['isProfessional'] = 0;
			}
			if (!isset($friends[$i]['isFriend']))
			{
				$friends[$i]['isFriend'] = 0;
			}
		}
		$i++; 
	}
	
	$blocked = $db->get_all(
  	array(
  		'table'=>'block_user',
  		'select'=>'block_user_id AS user_id',
  		'where'=>"block=1 AND user_id='".$_POST['user_id']."'"
  	)
  );
  $whoblocked = $db->get_all(
  	array(
  		'table'=>'block_user',
  		'select'=>'user_id AS user_id',
  		'where'=>"block=1 AND block_user_id='".$_POST['user_id']."'"
  	)
  );
      $result = array(
        'status'=> true,
        'data'=>$friends,
        'blocked_user'=>$blocked,
        'blocked_by'=>$whoblocked
      );

  echo json_encode($result);
}
?>