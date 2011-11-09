<?php 

	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/util/database.class.php";
	
	$username = Database::clean_string($_GET["username"]);
	$password = Database::clean_string($_GET["password"]);
	
	function is_user_login_valid ($name, $pass) {
		
		$db = $GLOBALS["db"] or new Databse();
			
		$query = "SELECT * FROM `phms_user` WHERE `username` = '$name' AND `password` = '$pass'";
	
		$result = $db->query($query);
		
		$accepted = false;
		
		$assoc;
		
		if (mysql_num_rows($result) == 1) {
			
			$assoc = mysql_fetch_assoc($result);
			
			if ($assoc["username"] == $name && $pass == $assoc["password"]);
				$accepted = true;
			
		}
		
		return $accepted;
		
	}
		
	if (isset($_GET["json"])) echo json_encode(Array("accepted" => is_user_login_valid($username, $password), "username" => $username, "password" => $password));

?>