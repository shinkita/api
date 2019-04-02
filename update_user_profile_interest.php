<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $interest_type      = $_POST['interest_type'];
 
	#######################update interest type####################
	$interest_data  = array(
		'table'	=>'user_detail',
		'data'	=>"interest='".$interest_type."'",
		'where'	=>"user_id='".$user_id."'"
            );
    $update_interest = $db->update($interest_data);
	#######################update interest type####################
    if($update_interest  > 0)
    {
        $result = array(
            'status' => true,
            'msg'=>"profile_interest updated"
            );
    } 
    else
    {
        $result = array(
            'status' => false,
            'msg'=>"something went wrong"
            );
    }
  echo json_encode($result);
}
?>
