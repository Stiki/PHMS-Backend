<?php

	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/util/database.class.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/user/login.php";
	
	$db = $GLOBALS["db"] or new Database();
	
	$username = Database::clean_string($_GET["username"]);
	$password = Database::clean_string($_GET["password"]);
	$name = Database::clean_string($_GET["name"]);
	
	$valid = false;
	$assoc = Array();

	if ($name != "" && is_user_login_valid($username, $password)) {
		
		$result = $db->query("SELECT * FROM `phms_projects` WHERE `name` = '$name'");
		if (mysql_num_rows($result) == 1) {
			
			$valid = true;
			$assoc = mysql_fetch_assoc($result);
			
		}
		
	}
	
	echo json_encode(array_merge(Array("valid" => $valid), $assoc));
	
?>