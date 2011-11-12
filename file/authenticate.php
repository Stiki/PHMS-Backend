<?php

	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/util/database.class.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/user/login.php";
	
	$db = $GLOBALS["db"] or new Databse();
	
	$filename = Database::clean_string($_GET["filename"]);
	$fileid = Database::clean_string($_GET["fileid"]);
	$project_id = Database::clean_string($_GET["project"]);
	$username = Database::clean_string($_GET["username"]);;
	$password = Database::clean_string($_GET["password"]);;
	
	$valid = false;
	$assoc = Array();
	$debug = "";
	
	if (is_user_login_valid($username, $password) && (isset($_GET["filename"]) && $filename != "") || (isset($_GET["fileid"]) && is_numeric($fileid))) {
		
		$query = "SELECT * FROM `phms_files` WHERE (`filename` = '$filename' OR `id` = '$fileid') AND `project` = '$project_id'";
		
		$result = $db->query($query);
		$debug .= "query ";
		
		if (mysql_num_rows($result) == 1) {
			
			$debug .= "one row ";
			$assoc = mysql_fetch_assoc($result);
			if ($assoc["filename"] == $filename || $assoc["fileid"] == $fileid) {
				$valid = true;
				$debug .= "same result ";
			}
			
		}
		
	} 
	
	echo json_encode(array_merge(Array("debug" => $debug, "valid" => $valid),$assoc));

?>