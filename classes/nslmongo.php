<?php 
class nslmongo extends base {
    private $connection;
	public $resource;
	private $db;
    public $__requirements = array("utils"); 
	function __construct(&$main, $db = "NSLWebEngineDataBase") {
		parent::__construct($main);
		$this->connection = new MongoClient();
		$this->db = $this->connection->selectDB($db);
	}
	function __get($name) {
		if(isset($this->db->$name))
			return $this->db->$name;
		return $this->db->createCollection($name);
	}
	function __call($fun, $args) {
		list($function, $table) = explode("…", strtolower(preg_replace("/([a-z])([A-Z])/", "$1…$2", $fun)));
		if(substr($table, -1) != "s")
			$table .= "s";
        if($table == "Persons")
            $table = "People";
		switch($function) {
			case "select":
			case "find":
			case "search":
				$cursor = new cursor($this->main);
				$cursor->obj = ($this->db->$table->find($args[0]));
				return $cursor;
			break;
            case "one":
            case "once":
            case "first":
                return $this->db->$table->findOne($args[0]);
            break;
			case "count":
			case "num":
				return $this->db->$table->count($args[0]);
			break;
			case "insert":
			case "new":
			case "ins":
			case "add":
				return $this->db->$table->insert($args[0]);
			break;
			case "update":
			case "edit":
			case "modify":
				return $this->db->$table->update($args[0], $args[1]);
			break;
			case "delete":
			case "remove":
			case "del":
			case "kill":
			case "destory":
			case "rem":
			case "rm":
				return $this->db->$table->remove($args[0]);
			break;
		}
	}
	function usedb($db) {
		$this->connection = $this->db = null; // Full Reset
		$this->connection = new MongoClient();
		$this->db = $this->connection->selectDB($db);
	}
	function hash($password) {
		return hash("whirlpool", base64_encode(hash("gost",base64_encode(md5($password.strlen($password)).strlen($password)))));
	}
	function salthash($salt, $password) {
		return base64_encode($this->hash($salt.$password)).base64_encode($this->hash($password.$salt)).base64_encode($this->hash($salt.$password.$salt));
	}
}
class cursor {
	public $obj;
    function __construct($main) {
        $this->main = $main;
    }
	function sort($arr = array()) {
		$this->obj->sort($arr);
		return $this;
	}
	function count() {
		return count(iterator_to_array($this->obj));
	}
	function fetch() {
		$obj = iterator_to_array($this->obj);
		if($this->count() == 1) {
			reset($obj);
            return $this->main->utils->toObject($obj[key($obj)]);
		}
		return $this->main->utils->toObject($obj);
	}
	function afetch() {
		return iterator_to_array($this->obj);
	}
}