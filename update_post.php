<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['post_id']))
  {
    $post_images = array();
    //update images
    if(isset($_POST['new_images']))
    {
      foreach (json_decode($_POST['new_images']) as $key => $value) {
        $post_images[] = $db->upload_image($value->image_data);
      }
    }
    if(isset($_POST['old_images']))
    {
      foreach (json_decode($_POST['old_images']) as $key => $value) {
        $post_images[] = $value->image_data;
      }
    }
    $post_images = json_encode($post_images);
    //update status
    $status = (isset($_POST['status']))?addslashes($_POST['status']):'';
    //video update
    $video = (isset($_POST['video']))?$_POST['video']:NULL;
    $file = (isset($_POST['file']))?$_POST['file']:NULL;
    $title = (isset($_POST['title']))?addslashes($_POST['title']):NULL;
    $lat = (isset($_POST['lattitute']))?$_POST['lattitute']:NULL;
    $lng = (isset($_POST['longitute']))?$_POST['longitute']:NULL;
    //event update not supported
    $date = $db->currentDate->format('Y-m-d H:i:s');
    $backup = "INSERT INTO b_post(post_id,user_id,profile_type,post_type,status,title,images,video,file,event,method,lng,lat,activated,deleted,update_date,date_time) SELECT id,user_id,profile_type,post_type,status,title,images,video,file,event,method,lng,lat,activated,deleted,update_date,date_time FROM post WHERE id = '".$_POST['post_id']."'";
    if($db->conn->query($backup))
    {
      if($stmt = $db->conn->prepare("UPDATE `post` SET `status`=?, `title`=?, `images`=?, `video`=?, `file`=?, `lng`=?, `lat`=?,`update_date`=? WHERE `id`=?"))
      {
        $stmt->bind_param("ssssssssi",$status,$title,$post_images,$video,$file,$lng,$lat,$date,$_POST['post_id']);      
        if($stmt->execute())
        {        
        //print_r($stmt);
          $row = $db->get_row(
            array(
              'table'=>'post',
              'where'=>"id='".$_POST['post_id']."'"
            )
          );
          if($stmt->affected_rows>0)
          {
            $result = array(
              'status' => true,
              'msg' => "post updated successfully",
              'data'=>$row
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
    }
    else
    {
      throw new Exception("Somethig went wrong");
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