<?php

	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/util/database.class.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "/pyrohawk/phms/user/login.php";
	
	$db = $GLOBALS["db"] or new Database();
	
	/* references:
	 * 
	 * PHMSFile.java
	 */
	
	$username = Database::clean_string($_GET["username"]);
	$password = Database::clean_string($_GET["password"]);
	$filename = Database::clean_string($_GET["filename"]);
	$fileid = Database::clean_string($_GET["fileid"]);
	$lastmodified = Database::clean_string($_GET["lastmodified"]);
	$lastcommit = Database::clean_string($_GET["lastcommit"]);
	$lastcommitid = Database::clean_string($_GET["lastcommitid"]);
	$project = Database::clean_string($_GET["project"]);
	
	$accepted = false;
	$assoc;
	
	if (($filename != "" || $fileid != "") && $lastcommit != "" && $lastmodified != "" && is_user_login_valid($username, $password)) {
		
		// Get current information
		$result = $db->query("SELECT * FROM `phms_files` WHERE `fileid` = '$fileid'");
		if (mysql_num_rows($result) == 1) {
			// File exists
			$query = "UPDATE `phms_files` (`id`,`filename`,`lastmodified`,`lastcommit`,`lastcommitid`,`project`) VALUES (NULL,'$filename','$lastmodified','$lastcommit','$lastcommitid','$project');";
			
			
		} else {
			// Create file
			$commit;
			if ($lastcommitid == -1) {
				
				$query = "SELECT * FROM `phms_commits` WHERE `lastcommitid` = '$lastcommitid'";
				$commit_result = $db->query($query);
				
				if (mysql_num_rows($commit_result) == 1) {
					// commit exists
					$commit = mysql_fetch_assoc($commit_result);
				}
				
			} else {
				
				$commit_id_result = $db->query("SELECT MAX(`id`) as `max_id` FROM `phms_commits`");
				$commit_id_result = mysql_fetch_assoc($commit_id_result);
				$lastcommitid = $commit_id_result["max_id"];
				
			}
			
			$query = "INSERT INTO `phms_files` (`id`,`filename`,`lastmodified`,`lastcommit`,`lastcommitid`,`project`) VALUES (NULL,'$filename','$lastmodified','$lastcommit','$lastcommitid','$project');";
			$query = $query + "INSERT INTO `phms_commits` (`id`,`timestamp`,`message`,`fileid`) VALUES (NULL,CURRENT_DATE,'$lastcommit','$fileid')";
			
			$result = $db->query($query);
			
			if (mysql_affected_rows($query) == 2) {
				// Success
				$query = "SELECT * FROM `phms_files` WHERE `id` = '$fileid'";
				$result = $db->query($query);
				if (mysql_num_rows($result)) {
					$accepted = true;
					$assoc = mysql_fetch_assoc($result);
				}
				
			}
			
		}
		
	}
	
	echo json_encode(array_merge(Array("accepted" => $accepted), $assoc))

?>