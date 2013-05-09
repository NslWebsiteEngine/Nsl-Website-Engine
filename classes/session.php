<?php
class session extends base {
	function __construct(&$main) {
        parent::__construct($main);
		session_start();
	}
	function __get($name) {
		return isset($_SESSION["NSLWebEngine::".$name]) ? $_SESSION["NSLWebEngine::".$name] : null;
	}
	function __set($name, $value) {
		$_SESSION["NSLWebEngine::".$name] = $value;
		return $value;
	}
	function __isset($name) {
		return isset($_SESSION["NSLWebEngine::".$name]);
	}
	function __unset($name) {
		unset($_SESSION["NSLWebEngine::".$name]);
	}
	function logged_in() {
		return isset($this->id);
	}
	function __call($method, $arguments) {
		list($option, $variable) = explode("_", strtolower(preg_replace("/([a-z])([A-Z])/", "$1_$2", $method)), 2);
		switch(strtolower($option)) {
			case "get":
				return $this->__get($variable);
			break;
			case "set":
				return $this->__set($variable, $arguments[0]);
			break;
			case "del":
			case "unset":
			case "delete":
				return $this->__unset($variable);
			break;
			case "is":
			case "isset":
				return $this->__isset($variable);
			break;
			case "dump":
				return var_dump($this->$variable);
			break;
			default:
				$this->main->trigger_error("The class session has no method ".$option, E_USER_WARNING);
				die();
			break;
		}
	}
}