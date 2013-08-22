<?php 
class mysql extends dbbase {

	public $separator = "%{ARG%}";
	public $connection;
	public $query = "";
	

	function __call($function, $args) { // $lib->main->database->mysql->selectUser("WHERE 'a' = '%a'", [])
		list($function, $table) = explode("//", preg_replace("/([a-z])([A-Z])/", "$1//$2", $function));
		if(substr($table, -1) != "s")
			$table .= "s";
		if($table == "Persons")
			$table = "People";
		switch($function) {
			case "select": 
			case "find":
			case "search":
				if(count($args) != 2)
					$this->main->trigger_error("You can pass only two args to this function");
				$query = "SELECT * FROM {$table}"." ".$args[0];
				return $this->query($query, $args[1]);
			break;
			case "count":
			case "num":
				if(count($args) != 2)
					$this->main->trigger_error("You can pass only two args to this function");
				$query = "SELECT COUNT(*) cnt FROM {$table}"." ".$args[0];
				return $this->query($query, $args[1])->fetch()->cnt;
			break;
			case "insert":
			case "new":
			case "ins":
			case "add":
				return $this->insert($table, $args[0]);
			break;
			case "update":
			case "edit":
			case "modify":
				$query = "UPDATE {$table} SET ";
				$els = array();
				foreach($data as $key => $value)
					$els[] = "{$key} = '".$this->escape($value)."'";
				$query .= implode(", ", $els);
				return $this->query($query, $args[0]);
			break;
		}
	}
	function setSeparator($begin, $end) {
		$this->separator = $begin."ARG".$end;
		return $this;
	}
	function connect($host, $username, $password, $database) {
		$this->connection = new mysqli($host, $username, $password, $database) || $this->main->trigger_error("Please check your connection data");
		return $this;
	}
	function query($query, $args = array()) {
		foreach($args as $key => $value)
			$query = str_replace(
				str_replace("ARG", $key, $this->separator),
				$this->escape($value),
				$query
			);
		$newthis = $this;
		$newthis->query = $this->connection->query($query);
		return $newthis;
	}
	function fetch() {
		return mysql_fetch_object($this->query);
	}
	function escape($arg) {
		return $this->connection->real_escape_string($arg);
	}
	function select($table, $where = null, $args = array()) {
		if(strlen($where) > 1 && !is_null($where))
			return $this->query("SELECT * FROM {$table} WHERE {$where}", $args);
		else
			return $this->query("SELECT * FROM {$table}", $args);
	}
	function insupdate($function, $table, $data) {
		$query = $function." {$table} SET "; 
		$elements = array();
		foreach($data as $key => $value)
			$elements[] = "{$key} = '".$this->escape($value)."'";
		$query .= implode(", ",$elements);
		return $this->query($query);
	}
	function insert($table, $data) {
		return $this->insupdate("INSERT INTO", $table, $data);
	}
	function update($table, $data) {
		return $this->insupdate("UPDATE", $table, $data);
	}
	function show($what = "TABLES") {
		return $this->query("SHOW ".strtoupper($what));
	}
	function num() {
		return mysql_num_rows($this->query);
	}
}
