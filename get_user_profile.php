<?php

if($_SERVER['REQUEST_METHOD']=='POST')
{
  require_once('Connect_class.php');
  $db = new db_connect();  
  $user_id      = $_POST['user_id'];
  $profile_type      = $_POST['profile_type'];
  $current_user =  $_POST['current_user'];
  if($_POST['user_id'] == $_POST['current_user'])
  {
  	$select = 'users.email as email, user_detail.name AS name,user_detail.dob AS dob,user_detail.gender AS gender,user_profile_about.bio,user_profile_about.phone,user_profile_about.address,user_profile_about.website,user_profile_about.education,user_profile_about.work,user_profile_about.description,user_profile_about.previous_work,profile_pic.profile_pic AS user_pic, 0 AS isFriend , 0 AS isFamiliy,0 AS isPro';
  }
  else{
  	$select = 'users.email as email, user_detail.name AS name,user_detail.dob AS dob,user_detail.gender AS gender,user_profile_about.bio,user_profile_about.phone,user_profile_about.address,user_profile_about.website,user_profile_about.education,user_profile_about.work,user_profile_about.description,user_profile_about.previous_work,profile_pic.profile_pic AS user_pic, (CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = "'.$user_id.'" OR friends_id = "'.$user_id.'") AND (user_id = "'.$current_user.'" OR friends_id = "'.$current_user.'") AND approved=1 AND profile_type=1 AND approved=1 LIMIT 1)=1 THEN 1 ELSE 0 END) AS isFriend,(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = "'.$user_id.'" OR friends_id = "'.$user_id.'") AND (user_id = "'.$current_user.'" OR friends_id = "'.$current_user.'") AND approved=1 AND profile_type=2 AND approved=1 LIMIT 1)=1 THEN 1 ELSE 0 END) AS isFamiliy,(CASE WHEN (SELECT approved FROM friends_list WHERE (user_id = "'.$user_id.'" OR friends_id = "'.$user_id.'") AND (user_id = "'.$current_user.'" OR friends_id = "'.$current_user.'") AND approved=1 AND profile_type=3 AND approved=1 LIMIT 1)=1 THEN 1 ELSE 0 END) AS isPro';
  }
  $data = array(
    'table'=>'users',
    'select'=>$select,
    'join'=>"LEFT JOIN user_profile_about ON users.id=user_profile_about.user_id AND user_profile_about.profile_type='".$profile_type."' LEFT JOIN user_detail ON user_detail.user_id=users.id LEFT JOIN profile_pic ON profile_pic.user_id=users.id AND profile_pic.profile_type='".$profile_type."'",
    'where'=>'users.id='.$user_id.''
  );
  
  $horoscop = 'Unknown';
  $user = $db->get_row($data);
  // print_r($user);    
          if(!empty($user['dob']))
          {
            $y=date('Y');
            $dob = explode(',', $user['dob']);
            $new_dob = new DateTime($dob[0].$y); 
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
          }
  $user['horoscope'] = $horoscop;        
  $result = array(
    'status'=> true,
    'user_data'=>$user
  );
  $multimedia_dir = 'mutimedia_dir';
  if(!empty($result['user_pic']) && $result['user_pic'] != '')
  {
      $result['user_pic'] = $db->$multimedia_dir.$u_detail['user_pic'];
  }
  if(!empty($result['user_data']['user_pic']) && $result['user_data']['user_pic'] != '')
  {
      $result['user_data']['user_pic'] = $db->$multimedia_dir.$result['user_data']['user_pic'];
  }
  $friend_list = $db->friends_list_datails_one($user_id,$profile_type);  
  $result['friend_list'] = $friend_list;
  $post[] = array();
  //print_r($_POST);exit;
  //$friends = $db->friends_lists($user_id,$profie_type);
  //print_r($friends);
  $data = array(
    'table'=>'post',
    'select'=>'post.id,post.user_id,post.post_type,post.method,post.title,user_detail.name AS username,profile_pic.profile_pic AS profile_pic,IF(post.post_type=4,(SELECT COUNT(*) FROM event_attand WHERE post_id=post.id),NULL) AS attanding,IF(post.post_type=4,(SELECT COUNT(id) FROM event_attand WHERE post_id=post.id AND user_id="'.$user_id.'"),NULL) AS is_i_attanding,(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND liked=1) AS no_of_likes,(SELECT COUNT(*) FROM comment WHERE post_id=post.id and deleted=0) AS no_of_comment,(SELECT COUNT(*) FROM share_post WHERE post_id=post.id and user_id="'.$user_id.'") AS no_of_share,CASE WHEN(SELECT COUNT(*) FROM post_like WHERE post_id=post.id AND user_id="'.$current_user.'" AND liked=1 )>0 THEN true ELSE false END AS isLikedByMe,CASE WHEN post.status IS NULL THEN "" ELSE post.status END AS status,post.images,CASE WHEN post.video IS NULL THEN "" ELSE post.video END AS video,CASE WHEN post.file IS NULL THEN "" ELSE post.file END AS file,CASE WHEN post.event IS NULL THEN "" ELSE post.event END AS event ,CASE WHEN post.lng IS NULL THEN "" ELSE post.lng END AS lng , CASE WHEN post.lat IS NULL THEN "" ELSE post.lat END AS lat, post.date_time ',
    'join'=>'LEFT JOIN event ON event.id=post.event JOIN user_detail ON post.user_id=user_detail.user_id JOIN profile_pic ON post.user_id=profile_pic.user_id AND profile_pic.profile_type=post.profile_type',
    'where'=>'post.user_id='.$user_id.' AND post.profile_type='.$profile_type.' AND post.activated=1 AND post.deleted=0 '.$id_limit.' ORDER BY post.id DESC '
  );
    $result['status']   = true;
    $result['posts']    = $db->get_post_all($data);
    $result['images']   = array();
    
            	###############saved interest Code Here########################
            
            $saveddata = array(
            'table'=>' user_detail ',
            'where' => " `user_id` = '" . $user_id . "'",
            );
            $result_saveddata = $db->get_all($saveddata);
            $interest_id=$result_saveddata[0]['interest'];
            				$interest_id= str_replace(",", "','", $interest_id); 
            $interest_data = array(
            'table'=>' interest_category_tbl',
            'where' => "`id` in('" . $interest_id . "')",
            );
            
            $result_interest = $db->get_all($interest_data);
            foreach($result_interest as $final_interest_result)
            {
            
            if($final_interest_result['status']==1)  
            $saved_interest[]=array('interest_id'=>$final_interest_result['id'],'interest_name'=>$final_interest_result['category_name']);
            }
            
            $result['saved_interest']   = $saved_interest;
            
            if(empty($saved_interest))
	         $result['saved_interest']=[];
            ###############saved interest Code Here########################

    foreach ($result['posts'] as $key=>$images)
    {
        if(!empty($images['images']) && count($images['images'])>0)
        {
            foreach ($images['images'] as $img)
            {
                $result['images'][] = $img; 
            }
        }
    }
  echo json_encode($result);
}
?>
