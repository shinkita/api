<?php
if(isset($_POST['message']) && !empty($_POST['msg_id']))
{
	require_once('Connect_class.php');
  	$db = new db_connect();
	$data  = array(
		'table'	=>'group_chat',
		'data'	=>" message='".addslashes($_POST['message'])."' ",
		'where'	=>" id='".$_POST['msg_id']."' "
	);
	$update = $db->update($data);
	if($update['status'] == true)
	{
		echo 'Message updated successfully';
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