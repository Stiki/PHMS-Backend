<?php

	define("USER_SALT", substr(md5("helloworld"),0,8));

	require_once "database.class.php";

	class User {
		
		//	Variable Declarations
		private $username = null;
		public $auth_level = null;
		private $connection = null;
		private $auth = false;
		
		static function register($user, $pass, $level) {
			
			$con = new Database();
			$user = Database::clean_string($user);
			$pass = md5(USER_SALT . Database::clean_string($pass));
			$level = Database::clean_string($level);
			
			$username = $con->query("SELECT `username` FROM `users` WHERE `username` = '$user'");
			
			if (mysql_num_rows($username) == 0) {
			
				$con->query("INSERT INTO `users` (username, password, level) VALUES ('$user','$pass','$level')");
				
				if (!mysql_error()) {
					return true;
				}
				
				return false;
			
			} else {
				
				return 2; // User exists
				
			}
			
		}
		
		function login ($user, $pass) {
				
			$this->connection = new Database();
			$user = Database::clean_string($user);
			$pass = md5(USER_SALT . Database::clean_string($pass));
			
			$user_assoc = $this->connection->query("SELECT * FROM `users` WHERE `username` = '$user' AND `password` = '$pass'");
			
			if (mysql_num_rows($user_assoc) == 1) {
				
				$this->auth = true;
			
				$user_assoc = mysql_fetch_assoc($user_assoc);
				$this->username = $user;
				$this->auth_level = $user_assoc["level"];
				//	Perform other var associations
				
				session_regenerate_id();
				$_SESSION["cur_user"] = $user_assoc;
				$_SESSION["cur_user"]["password"] = mysql_escape_string(stripslashes(htmlentities($pass)));
				$_SESSION["ra_token"] = md5($_SERVER["REMOTE_ADDR"]);
				
			} 
		
		}
		
		function is_logged_in() {
			
			if ($_SESSION["ra_token"] == md5($_SERVER["REMOTE_ADDR"]) && isset($_SESSION["cur_user"])) {
				
				return TRUE;
				
			}
			
			return FALSE;
			
		}
		
		function get_username() {
			return $this->username;
		}
		
		function get_auth_token() {
				
			switch($this->auth_level) {
				
				case 0:
					return 'tech';
				case 1:
					return 'admin';
				default:
					return 'tech';
				
			}
			
		}
		
		function __construct($user, $pass) {
			
			$this->login($user, $pass);
			
		}
		
	}

?>