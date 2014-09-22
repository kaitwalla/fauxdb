<?
class Fauxdb {
	function __construct() {
		// Database operations
		$this->db = new PDO(
			'mysql:host=localhost;dbname=fauxdb',
			'fauxdb',
			'vtPv7FuCoXW8Qd'
		);
		//Admin
		$this->ajaxes = $this->db->prepare('SELECT projects_url FROM fauxdb_users');
		$this->select_site = $this->db->prepare('SELECT * FROM fauxdb_storage WHERE site_id = :site_id');
		//Items
		$this->add = $this->db->prepare('INSERT INTO fauxdb_storage (doc_id,name,updated,site_id,json_name,fields_display,fields_hidden,fields_unused,search_text) VALUES (:gdoc_id,:name,NOW(),:site_id,:json_name,:fields_shown,:fields_details,:fields_hidden,:search_text)');
		$this->delete = $this->db->prepare('DELETE FROM fauxdb_storage WHERE id = :id');
	}

	//Items
	function get_dbs($info) {
		$new = $this->select_site;
		try {
			if ($new->execute(array('site_id'=>$info['site_id']))) {
				$rows = $new->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$curlurl = 'http://tools.ydr.com/fauxdb/widget.php?search_text='.$row['search_text'].'&id='.$row['id'].'&json_name='.$row['json_name'].'&projects_url='.$info['projects_url'].'&fields_hidden='.$row['fields_hidden'];
					$embed = $this::curlit($curlurl);
					echo "<tr data-id=\"".$row['id']."\">\n"
					."<td>".$row['name']."</td>\n"
					."<td><a href=\"https://docs.google.com/spreadsheets/d/".$row['doc_id']."\">Google Doc</a></td>\n"
					."<td><textarea disabled>".$curlurl."</textarea></td>\n"
					."<td><a href=\"".$info['projects_url']."?db=".$row['id']."\">Link</a></td>\n"
					."<td><i class=\"fi-pencil\"></i><i class=\"fi-x\"></i></td>"
					."</tr>";
				}
			}
			else {
				// Echo javascript function to call the message	
			}
		}
		catch (PDOException $ex) {
			// Echo javascript function to call the message
			return false;
		}
	}

	function add_db($info) {
		$new = $this->add;
		$info = new Table($info,true);
		try {
			if ($new->execute($info->params)) {
				$curlurl = $_SESSION['user']['gscript_url'].'?not='.urlencode(implode('NB',$info->fields_hidden)).'&id='.$info->gdoc_id;
				$data = $this::curlit($curlurl);
				file_put_contents('data/'.$info->json_name.'.js',$data);
				Interact::alert('success','Database created succesfully','ajax');
			}
		}
		catch (PDOException $ex) {
			echo $ex->message;
		}

	}

	function delete_db($id) {
		$new = $this->delete;
		try {
			if ($new->execute(array('id'=>$id))){
				// Also need to delete the json file
				Interact::alert('success','Database deleted successfully','ajax_refresh');
			}
			else {
				Interact::alert('error','No such database exists','ajax');
			}
		}
		catch (PDOException $ex) {
			Interact::alert('db_error',false);
		}
	}
	//Admin functions
	function check_ajax($val) {
		$wrong = false;
		$new = $this->ajaxes;
		try {
			if ($new->execute()) {	
				$rows = $new->fetchAll();
				foreach ($rows as $row) {
					if ($val == md5($row['projects_url'])) {
						$wrong = true;
						return true;
					}
				}
			}
			else {
				Interact::alert('db_error',false,'ajax');
			}
			if (!$wrong) {
				Interact::alert('error','You must be logged in to perform this function','ajax');
			}
		}
		catch (PDOException $ex) {
			Interact::alert('db_error',false,'ajax');
		}
	}

	function login($info) {
		$logprobs = true;
		$this->creds = new Credentials($info);
		$login = $this->db->prepare('SELECT * FROM fauxdb_users WHERE name = :name');
		try {
			if ($login->execute(array('name'=>$this->creds->user))) {
				$rows = $login->fetch(PDO::FETCH_ASSOC);
				if ($rows['password'] == md5('bu7cEsN9awaKwx'.$this->creds->pass)) {
					unset($rows['password']);
					$_SESSION['user'] = $rows;
					$_SESSION['fd_status'] = true;
					Interact::redirect();
				}
				else {
					$logprobs = false;
				}
			}
			else { $logprobs = false; }
			if (!$logprobs) {
				Interact::alert('error','Your site name or password is incorrect',true);
			}
		}
		catch (PDOException $ex) {
			Interact::alert('db_error',false,true);
		}
	}
	//Utility functions
	
	static function curlit($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		return(curl_exec($ch));
		curl_close($ch);
	}

	// Vars
	public $db;
	public $select;
	public $login;
	public $creds;
}

class Table {
	function __construct($var,$new=false) {
		$this->params = array();
		if ($new) {
			foreach ($var as $item=>$value) {
				if ($item == 'fields') {
					foreach ($value as $key => $arr) {
						$this->params[':fields_'.$key] = serialize($arr);
						$this->{'fields_'.$key} = $arr;
					}
				}
				else {
					$this->params[':'.$item] = $value;
					$this->{$item} = $value;
				}
			}
			$this->add_param('site_id',$_SESSION['user']['site_id']);
			$this->add_param('json_name',preg_replace("/[^a-zA-Z0-9]+/", "", $var->name).time());
		}
	}
	public function add_param($name,$val) {
		$this->params[':'.$name] = $val;
		$this->{$name} = $val;
	}
}

class Credentials {
	public $user;
	public $pass;
	function __construct($stuff) {
		if (isset($stuff['fd_site']) && isset($stuff['fd_pass'])) {
			$this->user = strtolower($stuff['fd_site']);
			$this->pass = $stuff['fd_pass'];
			unset($stuff['fd_site']);
			unset($stuff['fd_pass']);
		}
		else {
			Interact::alert('error','Please enter username or password',true);
		}
	}
}

class Interact {
	static function alert($type,$message,$redirect) {
		$msg = new stdClass();
		if ($type == 'db_error') {
			$msg->type = 'error';
			$msg->msg = 'Something went wrong with the database';
		}
		else {
			$msg->type = $type;
			$msg->msg = $message;
		}	
		if (!$redirect) {
			echo json_encode($msg);
		}
		else if ($redirect == 'ajax') {
			$_SESSION['msg'] = $msg;
			echo json_encode($msg);
		}
		else if ($redirect === true) {
			$_SESSION['msg'] = $msg;
			Header('Location:/fauxdb/admin.php');
		}
		else if ($redirect == 'ajax_refresh') {
			$_SESSION['msg'] = $msg;
			$send = new stdClass();
			$send->action = 'refresh';
			echo json_encode($send);
		}
		else {
			Header('Location:/fauxdb/'.$redirect.'.php');
		}
	}
	static function redirect($where=false) {
		$where = ($where) ? $where : 'admin.php'; 
		Header('Location:/fauxdb/'.$where);
	}
}


?>