<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
	$db = new db_connect();  
	if(isset($_POST['user_id']) && isset($_POST['current_user_id']))
	{		
		$isProfessional = $isFaimily = $isFriend = 0;
		if($stmt = $db->conn->prepare("SELECT profile_type FROM friends_list  WHERE (user_id=? OR friends_id=?) AND (user_id=? OR friends_id=?) AND approved=1"))
		{
			$stmt->bind_param("iiii",$_POST['user_id'],$_POST['user_id'],$_POST['current_user_id'],$_POST['current_user_id']);      
			if($stmt->execute())
			{        				
				$result = $stmt->get_result();            
				while($row = $result->fetch_assoc())
				{
					//echo $db->mutimedia_dir;die();
					if($row['profile_type'] == 1 )
					{
						$isFriend = 1;
					}
					elseif($row['profile_type'] == 2)
					{
						$isFaimily =1;
					}
					elseif($row['profile_type'] == 3)
					{
						$isProfessional = 1;	
					}
				}
				$result = array(
					'status' => true,
					'isfriends' => $isFriend,
					'isfaimily' => $isFaimily,
					'isprofessional' => $isProfessional,
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