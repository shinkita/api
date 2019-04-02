<?php
if(isset($_GET['temp_process']) && !empty($_GET['temp_process']))
{
	require_once('Connect_class.php');
  	$db = new db_connect();
	$data  = array(
		'table'	=>'users',
		'data'	=>" temp_process='' ,activated=1 ",
		'where'	=>" temp_process='".$_GET['temp_process']."' "
	);
	$update = $db->update($data);
	if($update['status'] == true)
	{
		echo 'Your account activated successfully';
	}
	else
	{
		echo 'page_expired';
	}

}
else
{
	echo 'page_expired';
}
?>