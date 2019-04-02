<?php
//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );

  require_once('Connect_class.php');
  $db = new db_connect();
  $data = array(
    'table'=>'tbl_headerbox1',
  );
  $faq = $db->get_post_all($data);
  echo json_encode($faq);
?>
