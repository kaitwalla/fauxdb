<?
error_reporting(-1);
ini_set('display_errors', 'On');

session_name('fauxdb');
session_start();

require_once('class_fauxdb.php');
$v = (!empty($_GET)) ? $_GET : $_POST;

if (isset($v['ajax'])) {
	$url = $v['ajax'].'?id='.$v['id'].'&reason=checker';
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	echo curl_exec($ch);
}

if (isset($v['purpose'])) {
	switch ($v['purpose']) {
		case 'login':
			$fd = new Fauxdb();
			$fd->login($v);
		break;
		case 'add':
			$fd = new Fauxdb();
			//$data = json_decode(urldecode($v['data']));
			$data = json_decode(stripcslashes($v['data']));
			$fd->add_db($data);
		break;
		case 'del_db':
			$fd = new Fauxdb();
			if ($fd->check_ajax($v['ajax'])) {
				$fd->delete_db($v['id']);
			}
		break;
	}
}
?>