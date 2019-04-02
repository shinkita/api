<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id      = $_POST['user_id'];
  $profile_type      = $_POST['profile_type'];
  $date = date('Y-m-d H:i:s');
    $sql = "";
    $array = array(        
        'bio',
        'phone',
        'address',
        'website',
        'education',
        'work',
        'description',
        'previous_work'
        );
    $init = 0;
    foreach ($array as $key => $field) 
    {
        if(isset($_POST[$field]) && !empty($_POST[$field]) && $_POST[$field] != '')
        {
            if($init == 0)
            {
                $sql .= $field." = '".$_POST[$field]."', ";
            }
        }
    }

    $sql .= "date_time='$date'";
    $data  = array(
		'table'	=>'user_profile_about',
		'data'	=>$sql,
		'where'	=>"user_id='".$user_id."' AND profile_type='".$profile_type."'"
            );
    $update = $db->update($data);
    if($update['affected_rows'] > 0)
    {
        $result = array(
            'status' => true,
            'msg'=>"profile_updated"
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
