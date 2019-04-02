<?php
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']) && isset($_POST['group_id']) && isset($_POST['pic']))
	{		
		$media = $db->upload_image($_POST['pic']);
		if(!empty($media) && !is_array($media))
		{
			$row = $db->get_row(array(
				'table'=>'chat_groups',
				'select'=>'pic',
				'where'=>"id='".$_POST['group_id']."'"
			));
			if(!empty($row['pic']))
			{
				unlink($db->upload_dir.DIRECTORY_SEPARATOR.$row['pic']) OR die('unlink failed');
			}
			$query = "UPDATE `chat_groups` SET pic=? WHERE `id`=? ";
			if(!($stmt = $db->conn->prepare($query)))
			{
				throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
			}
			if(!$stmt->bind_param("si",$media,$_POST['group_id']))
			{
				throw new Exception("binding Error : (" . $stmt->errno . ") " . $stmt->error);
			}      
			if(!$stmt->execute())
			{
				throw new Exception("execute Error: (" . $stmt->errno . ") " . $stmt->error);
			}
			$result = array(
				'status' => true,
				'pic'=>$db->mutimedia_dir.$media,
				'msg' => 'Updated successfully'
			);
			echo json_encode($result);
		}
		else
		{
			throw new Exception("image upload failed");			
		}
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
