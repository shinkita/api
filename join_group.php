<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']) && isset($_POST['group_id']) && isset($_POST['current_user']))
	{		$date = $db->currentDate->format('Y-m-d H:i:s');
			$admin = 0;
			$query = "INSERT INTO group_users (group_id,user_id,admin,add_by,date_time) VALUES(?,?,?,?,?) ";
		//echo $query;
			if(!($stmt = $db->conn->prepare($query)))
			{
				throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
			}
			if(!$stmt->bind_param("iiiis",$_POST['group_id'],$_POST['user_id'],$admin,$_POST['current_user'],$date))
			{
				throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
			}      
			if(!$stmt->execute())
			{
				throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
			}
			$result = array(
				'status' => true,
				'msg' => 'Added successfully'
			);
			echo json_encode($result);
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
