<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
global $db;
global $id_list;
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['comment_id']))
  {
    $deleted = 1;
    function get_native_list($comment_id,$con)
    {
      $query = "SELECT id FROM comment WHERE root_id='".$comment_id."'";
      $msql = $con->query($query);      
      if($msql->num_rows > 0)
      {
        while ($row = $msql->fetch_assoc()) {
          $id_list = get_native_list($row['id'],$con);
          $id_list[] = $row['id'];
          return $id_list;
        }
      }
    }
    $id_list = get_native_list($_POST['comment_id'],$db->conn);
    $id_list[] = $_POST['comment_id'];
    $id_list = implode(',', $id_list);
    if($stmt = $db->conn->prepare("UPDATE comment SET deleted=? WHERE id IN (?)"))
    {
      $stmt->bind_param("is",$deleted,$id_list);      
      if($stmt->execute())
      {        
        if($stmt->affected_rows>0)
        {
          $result = array(
            'status' => true,
            'msg' => "comment deleted successfully"
          );
          echo json_encode($result);
          die();
        }
        else
        {
          throw new Exception("Process incomplete");
        }
      }
      else
      {
        throw new Exception(filter_var($db->conn->error,FILTER_SANITIZE_STRING));
      }
      $stmt->close();
    }
    else
    {
      echo 'dasdas';
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