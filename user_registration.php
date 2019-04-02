<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();
  $name       = $_POST['name'];
  $email      = $_POST['email'];
  $password   = $_POST['password'];
  $dob        = $_POST['dob'];
  $gender     = $_POST['gender'];
  $p_pic_fr   = $_POST['pic_friend'];
  $p_pic_family   = $_POST['pic_family'];
  $p_pic_pro   = $_POST['pic_pro'];
  $path = '../viaspot_users/';
  $data = array(
    'table'=>'users',
    'where'=>" `email`='".$email."' ",
  );
  $count = $db->count($data);
  if($count>0)
  {
      $result = array(
        'status'=>false,
        'msg'=>'User already exist'
      );
  }
  else
  {
    $temp_process = sha1(time().$email.$_SERVER['SERVER_NAME']);
    $data = array(
      'table'=>'users',
      'field'=>'email,password,activated,temp_process',
      'values'=>array(array($email,sha1($password),0,$temp_process))
    );
    $result = $db->insert($data);
    function upload_pic($profile_pic,$path,$user_id)
    {
      $image_name = time().''.$user_id.''.rand().'.png';
      $encode = file_put_contents($path.''.$image_name, base64_decode($profile_pic));
      if($encode == true)
      {
        return $image_name;
      }
      else
      {
        return '';
      }
    }
    $friend_pic = upload_pic($p_pic_fr,$path,$result['inserted_id']);
    $family_pic = upload_pic($p_pic_family,$path,$result['inserted_id']);
    $pro_pic    = upload_pic($p_pic_pro,$path,$result['inserted_id']);
    $shadow     = array(
        'table'=>'profile_pic',
        'field'=>'user_id,profile_pic,profile_type',
        'values'=>array(array($result['inserted_id'],$friend_pic,1),array($result['inserted_id'],$family_pic,2),array($result['inserted_id'],$pro_pic,3))
    );
    $shadow1 = $db->insert(array('table'=>'user_detail','field'=>'user_id,name,dob,gender','values'=>array(array($result['inserted_id'],$name,$dob,$gender))));
    $shadow_result = $db->insert($shadow);
    $shadow3   = array(
        'table'=>'user_profile_about',
        'field'=>'user_id,profile_type,bio,phone,address,website,education,work,description,previous_work',
        'values'=>array(array($result['inserted_id'],1,'','','','','','','',''),array($result['inserted_id'],2,'','','','','','','',''),array($result['inserted_id'],3,'','','','','','','',''))
    ); 
    $shadow3 = $db->insert($shadow3);
    $apidir = 'api_dir';
    $url = $db->$apidir.'activate_account.php?temp_process='.$temp_process; 
    $subject="Welcome to Viaspot";
    $body   = "<!DOCTYPE html><html><body>";
    $body   .= "<h2>Viaspot</h2>";
    $body   .= "<p>Complete your registration by confirming your e-mail address using the following link:<a href='".$url."'>Activate your account</a><br>If the above URL does not work, try copying and pasting it into your browser. If you continue to have problems, please feel free to contact us via <welcome@viaspot.com> or reach us at www.viaspot.com<br>Activation Link: ".$url."<br>Sincerely,<br>Viaspot Team</p>";
    $body   .= "</body></html>";

    require_once('PHPMailer/src/Exception.php');
    require_once('PHPMailer/src/PHPMailer.php');
    require_once('PHPMailer/src/SMTP.php');
    $mail   = new PHPMailer(true);
    //$email = 'aysadarsh@gmail.com';
    //$body = 'testing mail';
    //$url = 'url';
    try
    {
      //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'viaspot.com;viaspot.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'welcome@viaspot.com';                 // SMTP username
      $mail->Password = 'Stcoinc$12';                           // SMTP password
      $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 465;                                    // TCP port to connect to
      //Recipients
      $mail->setFrom('welcome@viaspot.com', 'Viaspot.com');
      $mail->addAddress($email);     // Add a recipient
      //$mail->addAddress();               // Name is optional
      $mail->addReplyTo('welcome@viaspot.com', '');
     // $mail->addCC('');
      //$mail->addBCC('');

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
      //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

      //Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Welcome to viaspot';
      $mail->Body    = $body;
      $mail->AltBody = 'Activate your account by pasting this url to your browser, Activation Link: '.$url.'';

      $mail->send();
      $result['mail'] = "Message successfully sent!"; 
    }
    catch(Exception $e)
    {
      //echo $e;
      $result['mail'] = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo; 
    }
  }
  echo json_encode($result);
}
?>
