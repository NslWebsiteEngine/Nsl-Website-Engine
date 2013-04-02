<?php
class lib {
	protected $__keywords__;
	protected $__keywords_counter__ = 0xF0;
	function __construct() {
		$this->keyword("more");
		$this->keyword("ok");
		$this->keyword("already_there");
	}
	function add($protocol) {
		$protocol = strtolower($protocol);
		if(!isset($this->$protocol)) {
			$name = "classes/".strtolower($protocol).".php";
			if(file_exists($name))
				include $name;
			else
				die("NSL ERROR: Unable to find {$protocol} class.");
			$this->$protocol = new $protocol;
			if(isset($this->$protocol->__requirements)) {
				foreach($this->$protocol->__requirements as $k => $v)
					$this->add($v);
				return $this->keyword("more");
			}
			return $this->keyword("ok");
		}
		return $this->keyword("already_there");
	}
	function set($protocol) { return $this->add($protocol); }
	function using($protocol) { return $this->add($protocol); }
	function protocol($protocol) {
		$protocol = strtolower($protocol);
		return isset($this->$protocol) ? $this->$protocol : null;
	}
	function keyword($k) {
		if(!isset($this->__keywords__[$k])) {
			$this->__keywords__[$k] = $this->__keywords_counter__;
			$this->__keywords_counter__ += strlen($k);
		}
		return dechex($this->__keywords__[$k]);
	}
	static function toObject($array) {
		$obj = new stdClass();
		foreach ($array as $key => $val)
			$obj->$key = is_array($val) ? self::toObject($val) : $val;
		return $obj;
	}
	static function toArray($d) {
		if (is_object($d))
			$d = get_object_vars($d);
		if (is_array($d))
			return array_map(__FUNCTION__, $d);
		else
			return $d;
	}
}
