<?php
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']) && isset($_POST['group_id']) && isset($_POST['name']))
	{				
			$group_name = addslashes($_POST['name']);
			$query = "UPDATE `chat_groups` SET name=? WHERE `id`=? ";
		//echo $query;
			if(!($stmt = $db->conn->prepare($query)))
			{
				throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
			}
			if(!$stmt->bind_param("si",$group_name,$_POST['group_id']))
			{
				throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
			}      
			if(!$stmt->execute())
			{
				throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
			}
			$result = array(
				'status' => true,				
				'msg' => 'Updated successfully'
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
}
?>
