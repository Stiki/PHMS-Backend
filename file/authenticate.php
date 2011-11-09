<?php

	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/util/database.class.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/user/login.php";
	
	$db = $GLOBALS["db"] or new Databse();
	
	$filename = Database::clean_string($_GET["filename"]);
	$fileid = Database::clean_string($_GET["fileid"]);
	$project_id = Database::clean_string($_GET["project_id"]);
	$username = Database::clean_string($_GET["username"]);;
	$password = Database::clean_string($_GET["password"]);;
	
	$valid = false;
	$assoc;
	
	if (is_user_login_valid($username, $passwor) && (isset($_GET["filename"]) && $filename != "") || (isset($_GET["fileid"]) && is_numeric($fileid))) {
		
		$query = "SELECT * FROM `files` WHERE (`filename` = '$filename' OR `fileid` = '$fileid') AND `projectid` = $project_id";
		
		$result = $db->query($query);
		
		if (mysql_num_rows($result) == 1) {
			
			$assoc = mysql_fetch_assoc($result);
			if ($assoc["filename"] == $filename || $assoc["fileid"] == $fileid) {
				$valid = true;
			}
			
		}
		
	} 
	
	echo json_encode(array_merge(Array("valid" => true),$assoc));

?>