<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connect_class.php');
    $db = new db_connect();
    $file_name = $_FILES['myFile']['name'];
    $file_size = $_FILES['myFile']['size'];
    $file_type = $_FILES['myFile']['type'];
    $temp_name = $_FILES['myFile']['tmp_name'];
    $ext = pathinfo($_FILES['myFile']['name'])['extension'];
    $location = $db->upload_dir;
    $vidio = time() . '' . $user_id . '' . rand() . '.' . $ext;
    $uploaded = move_uploaded_file($temp_name, __DIR__.DIRECTORY_SEPARATOR.$location . DIRECTORY_SEPARATOR . $vidio);
    if ($uploaded == true) {
        $result = array(
            'status' => true,
            'msg' => 'Upload successfully!',
            'name' => $vidio,
            'video_url'=>$db->mutimedia_dir
        );
    } else {
        $result = array(
            'status' => false,
            'msg' => 'File not uploaded : ' . $_FILES["myFile"]["error"]
        );
    }
    echo json_encode($result);
}
?>