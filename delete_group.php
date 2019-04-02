<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['group_id']))
	{		
			$query = "UPDATE `chat_groups` SET deleted=1 WHERE `id`=? ";
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
			$stmt->close();
			$query2 = "UPDATE `group_chat` SET `deleted`=1 WHERE `group_id`=? ";
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
			$stmt->close();
			$result = array(
				'status' => true,
				'msg' => 'successful'
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
