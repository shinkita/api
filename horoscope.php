<?php
// ini_set( 'error_reporting', E_ALL );
// ini_set( 'display_errors', true );
// print_r($_POST);
require_once('Connect_class.php');
try{
  $db = new db_connect();  
  if(isset($_POST['user_id']))
  {    
  	$y=date('Y');
      if($stmtone = $db->conn->prepare("SELECT user_detail.dob FROM users JOIN user_detail ON user_detail.user_id=users.id WHERE users.id=? AND activated=1 AND deleted=0"))
      {
        $stmtone->bind_param('i',$_POST['user_id']);
        if($stmtone->execute())
        {
          $row = $stmtone->get_result();
          $user = $row->fetch_assoc();
          if(!(count($user)>0))
          {
          	throw new Exception("User not found");
          }
          if(!empty($user))
          {
          	$dob = explode(',', $user['dob']);
          	$new_dob = new DateTime($dob[0].$y);	
          }
          else
          {
          	throw new Exception("Dob is empty");
          	
          }
          $Arise_start = new DateTime('21-03-'.$y);
          $Arise_end = new DateTime('20-04-'.$y);
          $Taurus_start = new DateTime('21-04-'.$y);
          $Taurus_end = new DateTime('20-05-'.$y);
          $Gemini_start = new DateTime('21-05-'.$y);
          $Gemini_end = new DateTime('20-06-'.$y);
          $Cancer_start = new DateTime('21-06-'.$y);
          $Cancer_end = new DateTime('20-07-'.$y);
          $Leo_start = new DateTime('21-07-'.$y);
          $Leo_end = new DateTime('20-08-'.$y);
          $Virgo_start = new DateTime('21-08-'.$y);
          $Virgo_end = new DateTime('20-09-'.$y);
          $Libra_start = new DateTime('21-09-'.$y);
          $Libra_end = new DateTime('20-10-'.$y);
          $Scorpio_start = new DateTime('21-10-'.$y);
          $Scorpio_end = new DateTime('20-11-'.$y);
          $Sagittarius_start = new DateTime('21-11-'.$y);
          $Sagittarius_end = new DateTime('20-12-'.$y);
          $Capricorn_start = new DateTime('21-12-'.$y);
          $Capricorn_end = new DateTime('20-01-'.$y);
          $Aquarius_start = new DateTime('21-01-'.$y);
          $Aquarius_end = new DateTime('20-02-'.$y);
          $Pisces_start = new DateTime('21-02-'.$y);
          $Pisces_end = new DateTime('20-03-'.$y);
          if($new_dob > $Arise_start && $new_dob < $Arise_end)
          {
          	$horoscop = 'Arise';
          }
          elseif($new_dob > $Taurus_start && $new_dob <  $Taurus_end)
          {
          	$horoscop = 'Taurus';
          }
          elseif($new_dob > $Gemini_start && $new_dob <  $Gemini_end)
          {
          	$horoscop = 'Gemini';
          }
          elseif($new_dob > $Cancer_start && $new_dob <  $Cancer_end)
          {
          	$horoscop = 'Cancer';
          }
          elseif($new_dob > $Leo_start && $new_dob <  $Leo_end)
          {
          	$horoscop = 'Leo';
          }
          elseif($new_dob > $Virgo_start && $new_dob <  $Virgo_end)
          {
          	$horoscop = 'Virgo';
          }
          elseif($new_dob > $Libra_start && $new_dob <  $Libra_end)
          {
          	$horoscop = 'Libra';
          }
          elseif($new_dob > $Scorpio_start && $new_dob <  $Scorpio_end)
          {
          	$horoscop = 'Scorpio';
          }
          elseif($new_dob > $Sagittarius_start && $new_dob <  $Sagittarius_end)
          {
          	$horoscop = 'Sagittarius';
          }
          elseif($new_dob > $Capricorn_start && $new_dob <  $Capricorn_end)
          {
          	$horoscop = 'Capricorn';
          }
          elseif($new_dob > $Aquarius_start && $new_dob <  $Aquarius_end)
          {
          	$horoscop = 'Aquarius';
          }
          elseif($new_dob > $Taurus_start && $new_dob <  $Taurus_end)
          {
          	$horoscop = 'Pisces';
          }
          elseif($new_dob > $Pisces_start && $new_dob <  $Pisces_end)
          {
          	$horoscop = 'Pisces';
          }
          else
          {
          	$horoscop = 'Unknown';
          }
          $result = array(
              'status' => true,
              'msg' => $horoscop
            );
            echo json_encode($result);
            die();
        }
        else{
          throw new Exception("Query execution failed");
        }
      } else{
        throw new Exception("prepare command failed");
      }
      $stmtone->close();
     
            
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