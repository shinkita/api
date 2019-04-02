<?php 
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $user_id = $_POST['user_id'];
    $a_name = $_POST['album_name'];
    $a_location = $_POST['album_location'];
    $a_description = $_POST['album_description'];
    $a_see = $_POST['who_can_see'];
    $profile = $_POST['profile'];
    $deleted = 0;
    $query = "INSERT INTO user_album_table(user_id,user_album_name,user_album_location,user_album_description,who_can_see,profile,is_deleted) VALUES($user_id,'$a_name','$a_location','$a_description','$a_see','$profile','$deleted')";
    $insert = $con->query($query);
    $user_id = $con->insert_id;
    if ($insert === TRUE) 
    {
        $result = array(
        'msg'=>'album created',
        'status'=>'success'
        );
    }
    else
    {
        $result = array(
        'msg'=>$con->error,
        'status'=>'failure'
        );
    }
    echo json_encode($result);
}
?>