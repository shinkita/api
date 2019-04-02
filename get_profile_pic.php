  <?php
 // ini_set( 'error_reporting', E_ALL );
 // ini_set( 'display_errors', true );
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $user_id    = $_POST['user_id']; 
  $profile_type =$_POST['profile_type'];
  $data = array(
    'table'=>'profile_pic',
    'select'=>'profile_pic',
    'where'=>"user_id='".$user_id."' AND profile_type='".$profile_type."'"
  );
  $result = $db->get_row($data);
  $multimedia_dir = "mutimedia_dir";
  $result['profile_pic'] = $db->$multimedia_dir.$result['profile_pic'];
  $result['status'] = true;      
  echo json_encode($result); 
}
?>