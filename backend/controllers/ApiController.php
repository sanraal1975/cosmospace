<?php

	error_reporting(E_ALL|E_STRICT);
	ini_set('display_errors', '1');

	include "../components/SQL.php";
	include "../components/Encrypter.php";

	$post = $_POST;
	$result = array();

	if(!array_key_exists('function',$post)) {
		http_response_code(400);
		$result['msg'] = 'Function not set';
	}
	else
	{
		$function = $post['function'];
		$function = trim($function);
		switch($function)
		{
			case 'searchEntry':
				$result = searchEntry();
				break;
			case 'addEntry':
				$result = addEntry();
				break;
			case 'getToken':
				$result = getToken();
				break;
			case 'getAllEntries':
				$result = getAllEntries();
				break;
			default:
				http_response_code(400);
				$result['msg'] = 'Unknown function';
				break;
		}
	}
	$result = json_encode($result);
	echo $result;

	function getToken()
	{
		$now = date('Y-m-d H:i:s');
		$expiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));
		$token = "guest ".date('Y-m-d H:i:s');
		$encrypter = new Encrypter();
		$token = $encrypter->encrypt($token);
		$query = "insert into token(token,expiration) values (:token , :expiration)";
		$sql = new SQL();
		$sql->addStringParam(':token', $token);
		$sql->addStringParam(':expiration', $expiration);
		$result = $sql->execute($query);
		return ['token' => $token];
	}

	function checkToken()
	{
		global $post;
		if(!array_key_exists('token',$post))
		{
			http_response_code(400);
			return ['msg' => 'Missing Token'];
		}
		$token = $post['token'];
		$query = "select token from token where token = :token and expiration > :expiration";
		$sql = new SQL();
		$sql->addStringParam(":token",$token);
		$now = date('Y-m-d H:i:s');
		$sql->addStringParam(":expiration",$now);
		$result = $sql->queryRow($query);
		if(!is_array($result))
		{
			http_response_code(404);
			return ['msg' => 'Token not found'];
		}
		else
		{
			if(array_key_exists('status', $result)) 
			{
				http_response_code(500);
				return ['msg' => 'Unexpected error while token checking'];
			}
			else return TRUE;
		}
		return TRUE;
	}

	function getAllEntries()
	{
		$token = checkToken();
		if($token !== TRUE) return $token;
		$query = "select * from directory";
		$sql = new SQL();
		$result = $sql->queryAll($query);
		if(!count($result)) 
		{
			http_response_code(404);
			return ['msg' => 'No entries found'];
		}
		$result = json_encode($result);
		return ['entries' => $result];
	}

	function addEntry()
	{
		global $post;
		$token = checkToken();
		if($token !== TRUE) return $token;
		$query = "insert into directory(phone,lastname) values(:phone,:lastname)";
		$sql = new SQL();
		$sql->addStringParam(':phone',$post['phone']);
		$sql->addStringParam(':lastname',$post['lastname']);
		$result = $sql->execute($query);
		if(is_array($result)) 
		{
			http_response_code(500);
			return ['msg' => 'Error on inserting values. ¿ Duplicated pair ?'];
		}
		return ['msg' => 'Entry saved correctly'];
	}

	function searchEntry()
	{
		global $post;
		$token = checkToken();
		if($token !== TRUE) return $token;
		$query = "";
		if($post['searchfield']==0)
		{
			$query = "select * from directory where phone like :inputsearch";
		}
		else
		{
			$query = "select * from directory where lastname like :inputsearch";
		}
		$sql = new SQL();
		$sql->addStringParam(':inputsearch','%'.$post['inputsearch'].'%');
		$result = $sql->queryAll($query);
		if(!count($result)) 
		{
			http_response_code(404);
			return ['msg' => 'No entries found'];
		}
		$result = json_encode($result);
		return ['entries' => $result];
	}

?>