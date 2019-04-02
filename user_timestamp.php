<?php
require_once('Connect_class.php');
$db = new db_connect();
try{	
	if(isset($_POST['user_id']))
	{
		$user_id = $_POST['user_id'];
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if(!isset($user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		$device = 'Unknown';
    // https://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
		$os_array = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android/i'            =>  'Android',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile'
                    );


    // https://github.com/ahmad-sa3d/php-useragent/blob/master/core/user_agent.php
		// $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
		// $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

		foreach ($os_array as $regex => $value) {
			if (preg_match($regex, $user_agent)) {
				$device = $value;
				break;
			}
		}
		$data_sub = array(
			'table'=>'login_history',
			'field'=>'user_id,ip,device',
			'values'=>array(array($user_id,$ip,$device))
		);
		$result = $db->insert($data_sub);
		if($result['status'] === true)
		{
			echo json_encode($result);
			die();

		}
		else
		{
			throw new Exception("Somthing wrong");
		}
	}
	else
	{
		throw new Exception("Missing required field");
	}
}
catch(Exception $ex)
{
	$result = array(
		'status' => false,
		'msg' => $ex->getMessage()
	);
	echo json_encode($result);
}
?>