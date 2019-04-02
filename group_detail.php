<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']) && isset($_POST['group_id']))
	{
		$query = "SELECT `group_users`.`user_id` AS `user_id`, `user_detail`.`name` AS `name`, IF(`profile_pic`.`profile_pic` != '',CONCAT('".$db->mutimedia_dir."',`profile_pic`.`profile_pic`),'') AS `pic` , IF(`group_users`.`add_by`=0,TRUE,FALSE) AS `isAdmin`   FROM `group_users` JOIN `users` ON `users`.`id`=`group_users`.`user_id` JOIN `user_detail` ON `user_detail`.`user_id`=`group_users`.`user_id` JOIN `profile_pic` ON `profile_pic`.`user_id`=`group_users`.`user_id` AND `profile_type`=3 WHERE `group_users`.`group_id`=? AND `users`.`deleted`=0 AND `users`.`activated`=1";
		//echo $query;
		if(!($stmt = $db->conn->prepare($query)))
		{
			throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
		}
		if(!$stmt->bind_param("i",$_POST['group_id']))
		{
			throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
		}      
		if(!$stmt->execute())
		{
			throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
		}
		$data = $stmt->get_result();
		$rows = array();
		while($row = $data->fetch_assoc())
		{
			$rows[] = $row;
		}
		$result = array(
			'status' => true,
			'data' => $rows
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
