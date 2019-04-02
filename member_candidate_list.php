<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']) && isset($_POST['group_id']))
	{
		$query = "SELECT user_id,friends_id,profile_type FROM friends_list WHERE (user_id =? OR friends_id = ?) AND approved=1";
		//echo $query;
		if(!($stmt = $db->conn->prepare($query)))
		{
			throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
		}
		if(!$stmt->bind_param("ii",$_POST['user_id'],$_POST['user_id']))
		{
			throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
		}      
		if(!$stmt->execute())
		{
			throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
		}
		$data = $stmt->get_result();
		$rows = array();
		$list = array();
		while($row = $data->fetch_assoc())
		{
			$rows[] = $row;
			if (!in_array($row['friends_id'], $list)) {
                    $list[] = $row['friends_id'];
                }
                if (!in_array($row['user_id'], $list)) {
                    $list[] = $row['user_id'];
                }
		}
		$list = array_diff($list, array($user_id));
		$stmt->close();
		$query = "SELECT user_id FROM group_users WHERE group_id='".$_POST['group_id']."'";
		if(!($stmt = $db->conn->prepare($query)))
		{
			throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
		}     
		if(!$stmt->execute())
		{
			throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
		}
		$data = $stmt->get_result();
		$group_id = array();
		while($row = $data->fetch_assoc())
		{
			$group_id[] = $row['user_id'];
		}
		$query = "SELECT user_detail.user_id AS id,name,profile_pic.profile_pic AS user_pic FROM user_detail JOIN users ON user_detail.user_id=users.id JOIN profile_pic ON user_detail.user_id = profile_pic.user_id AND profile_pic.profile_type=(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=1 LIMIT 1)=1 THEN 1 WHEN (SELECT approved FROM friends_list WHERE (user_id = '$user_id' OR friends_id = '$user_id') AND (user_id = user_detail.user_id OR friends_id = user_detail.user_id) AND approved=1 AND profile_type=2 LIMIT 1)=1 THEN 2 ELSE 3 END) WHERE user_detail.user_id IN (" . implode(',', $list) . ")";
		if(!($stmt = $db->conn->prepare($query)))
		{
			throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
		}     
		if(!$stmt->execute())
		{
			throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
		}
		$data = $stmt->get_result();
		$fin = array();
		while($row = $data->fetch_assoc())
		{
			if(!in_array($row['id'],$group_id))
			{
				//echo $row['id'];
				if (!empty($row['user_pic']) || $row['user_pic'] != '') {
                    $row['user_pic'] = $db->mutimedia_dir . $row['user_pic'];
                }
				$fin[] = $row;
			}
		}
		$refine = array_diff($fin, $group_id);
		$result = array(
			'status' => true,
			'data' => $refine,			
		);
		echo json_encode($result);
		$stmt->close();
	}
	else
	{
		throw new Exception("missing required field");
	}
}
catch(Exception $ex)
{
	$result = array(
		'status' => false,
		'msg' => $ex->getMessage()
	);
	echo json_encode($result);
	die();
}
?>
