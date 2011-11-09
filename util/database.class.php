<?php

include_once "login.db.php";

class Database {
	public $connection;
	public $open = false;
	public $last_error;
	static $queries_performed = 0;
	
	public static function clean_string($string) {
		return stripslashes(htmlentities($string, ENT_QUOTES));
	}
	
	public function connect($db_database) {
		$db_hostname = DB_SERVER;
		$db_username = DB_USERNAME;
		$db_password = DB_PASSWORD;
		$this->connection = mysql_connect($db_hostname, $db_username, $db_password);
		if ($this->connection) {
			if (mysql_select_db($db_database)) {
				$this->open = true;
			} else {
				$this->last_error = mysql_error();
				throw new Exception("Unable to select database: " . mysql_error());
			}
		} else {
			$this->last_error = mysql_error();
			throw new Exception("Unable to connect to MySQL: " . mysql_error());
		}
	}
	
	public function disconnect() {
		if ($this->open) {
			@mysql_close($this->connection);
			$this->open = false;
		} else {
			$this->last_error = mysql_error();
		}
	}
	
	public function __construct($name = DB_DATABASE) {
		$this->connect($name);
	}
	
	public function __destruct() {
		$this->disconnect();
	}
	
	public function query($query_str) {
		if ($this->open) {
			$result = mysql_query($query_str);
			if ($result) {
				return $result;
			} else {
				$this->last_error = mysql_error();
				throw new Exception("Unable to perform query: " . mysql_error());
			}
		} else {
			$this->last_error = mysql_error() . " - " . $this->open;
			throw new Exception("No database available.");
		}
	}
}

$GLOBALS["db"] = new Database();

?>