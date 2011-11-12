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
	$assoc = Array();
	$debug = "";
	
	if (($filename != "" || $fileid != "") && $lastcommit != "" && is_user_login_valid($username, $password)) {
		
		// Get current information
		$result = $db->query("SELECT * FROM `phms_files` WHERE `id` = '$fileid'");
		if (mysql_num_rows($result) == 1) {
			// File exists
			$query = "UPDATE `phms_files` (`id`,`filename`,`lastmodified`,`lastcommit`,`lastcommitid`,`project`) VALUES (NULL,'$filename','$lastmodified','$lastcommit','$lastcommitid','$project');";
			$result = $db->query($query);
			if (mysql_affected_rows($result) == 1) {
				$valid = true;
				$assoc = mysql_fetch_assoc($query);
			}
			$debug .= "exists ";
			
		} else {
			// Create file
			$commit;
			if ($lastcommitid != -1) {
				
				$query = "SELECT * FROM `phms_commits` WHERE `id` = '$lastcommitid'";
				$commit_result = $db->query($query);
				
				if (mysql_num_rows($commit_result) == 1) {
					// commit exists
					$commit = mysql_fetch_assoc($commit_result);
					$debug .= "commit exists ";
				}
				
			} else {
				
				$commit_id_result = $db->query("SELECT MAX(`id`) as `max_id` FROM `phms_commits`");
				$commit_id_result = mysql_fetch_assoc($commit_id_result);
				$lastcommitid = $commit_id_result["max_id"];
				$debug .= "created commit id " . $lastcommitid;
				
			}
			
			// 
			
			$query = "INSERT INTO `phms_files` (id,filename,lastmodified,lastcommit,lastcommitid,project) VALUES (NULL,'$filename',CURRENT_DATE,'$lastcommit','$lastcommitid','$project'); ";
			$result_1 = $db->query($query);
			if (mysql_affected_rows() == 1) {
				$debug .= "created file entry ";
						
				$result = $db->query("SELECT MAX(`id`) as `max_id` FROM `phms_files`");
				$max_id = mysql_fetch_assoc($result);
				$max_id = $max_id["max_id"];
				$query = "SELECT * FROM `phms_files` WHERE `id` = '$max_id'";
				$result_file = $db->query($query);
				$assoc = mysql_fetch_assoc($result_file);
				$fileid = $assoc["id"];
				
				if (mysql_num_rows($result_file) == 1) {
			
					$query = "INSERT INTO `phms_commits` (id,timestamp,message,fileid) VALUES (NULL,NOW(),'$lastcommit',$fileid);";
					$result_2 = $db->query($query);
					$debug .= $result_2;
					
					if (mysql_affected_rows() == 1) {
						$debug .="created commit entry ";
				
						$accepted = true;
						$debug .= "accepted ";
					}
				}
			}
		}
		
	}
	
	echo json_encode(@array_merge(Array("debug" => $debug,"accepted" => $accepted), $assoc))

?>