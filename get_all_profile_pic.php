<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']))
	{		
		if($stmt = $db->conn->prepare("SELECT * FROM profile_pic WHERE user_id=?"))
		{
			$stmt->bind_param("i",$_POST['user_id']);      
			if($stmt->execute())
			{        
				$friend_pic = $faimily_pic = $professional_pic = '';
				$result = $stmt->get_result();            
				while($row = $result->fetch_assoc())
				{
					//echo $db->mutimedia_dir;die();
					if($row['profile_type'] == 1 && $row['profile_pic'] != '')
					{
						$friend_pic = $db->mutimedia_dir .$row['profile_pic'];
					}
					elseif($row['profile_type'] == 2 && $row['profile_pic'] != '')
					{
						$faimily_pic = $db->mutimedia_dir .$row['profile_pic'];	
					}
					elseif($row['profile_type'] == 3 && $row['profile_pic'] != '')
					{
						$professional_pic = $db->mutimedia_dir .$row['profile_pic'];	
					}
				}
				$result = array(
					'status' => true,
					'friends_pic' => $friend_pic,
					'faimily_pic' => $faimily_pic,
					'professional_pic' => $professional_pic,
				);
				echo json_encode($result);
				die();

			}
			else
			{
				throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
			}
			$stmt->close();
		}
		else
		{
			echo 'prepare statement failed';
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