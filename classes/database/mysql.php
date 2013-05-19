<?php 
class mysql extends base {

	public $separator = "%{ARG%}";
	public $connection;
	public $query = "";
	
	function setSeparator($begin, $end) {
		$this->separator = $begin."ARG".$end;
		return $this;
	}
	function connect($host, $username, $password, $database) {
		$this->connection = new mysqli($host, $username, $password, $database) || $this->main->trigger_error("Please check your connection data");
		return $this;
	}
	function query($query, $args = []) {
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
	function select($table, $where = null, $args = []) {
		if(strlen($where) > 1 && !is_null($where))
			return $this->query("SELECT * FROM {$table} WHERE {$where}", $args);
		else
			return $this->query("SELECT * FROM {$table}", $args);
	}
	function insupdate($function, $table, $data) {
		$query = $function." {$table} SET "; 
		$elements = [];
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
}