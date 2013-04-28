<?php
class lib {
	protected $__keywords__;
	protected $__keywords_counter__ = 0xF0;
	protected $__removed = [];
	function __construct() {
		$this->keyword("more");
		$this->keyword("ok");
		$this->keyword("already_there");
	}
	function __get($name) {
		if($name != "__removed")
			if(!isset($this->$name))
				$this->trigger_error("Class {$name} isn't loaded");
	}
	function add($protocols) {
		if(is_array($protocols)) {
			$returns = [];
			foreach($protocols as $protocol)
				$returns[] = $this->_add($protocol);
			return $returns;
		}elseif(is_string($protocols))
			return $this->_add($protocols);
		return false;
	}
	function set($protocol) { return $this->add($protocol); }
	function using($protocol) { return $this->add($protocol); }
	function protocol($protocol) {
		$protocol = strtolower($protocol);
		return isset($this->$protocol) ? $this->$protocol : null;
	}
	function destroy($protocol) {
		if(isset($this->__removed[$protocol]))
			$this->trigger_error("Protocol {$protocol} is already unset");
		if(!isset($this->$protocol))
			$this->trigger_error("NSL ERROR: The protocol {$protocol} isn't loaded");
		$removed = $this->__removed;
		$removed[$protocol] = $this->$protocol;
		$this->__removed = $removed;
		$this->$protocol = null;
		return $this->keyword("ok");
	}
	function keyword($k) {
		if(!isset($this->__keywords__[$k])) {
			$this->__keywords__[$k] = $this->__keywords_counter__;
			$this->__keywords_counter__ += strlen($k);
		}
		return dechex($this->__keywords__[$k]);
	}
	function json_decode_file($file) {
		return json_decode(file_get_contents("{$file}"));
	}
	static function toObject($array) {
		$obj = new stdClass();
		foreach ($array as $key => $val)
			$obj->$key = is_array($val) ? self::toObject($val) : $val;
		return $obj;
	}
	static function toArray($d) {
		if(is_object($d))
			$d = get_object_vars($d);
		if(is_array($d))
			return array_map(__FUNCTION__, $d);
		else
			return $d;
	}
	function trigger_error($error) {
		die("NSL ERROR: {$error}");
	}
	function _add($protocol) {
		$protocol = strtolower($protocol);
		if(!isset($this->$protocol)) {
			if(isset($this->__removed[$protocol])) {
				$this->$protocol = $this->__removed[$protocol];
				unset($this->__removed[$protocol]);
				return $this->keyword("ok");
			} else {
				$name = "classes/".strtolower($protocol).".php";
				if(file_exists($name))
					include_once $name;
				else
					$this->trigger_error("Unable to find {$protocol} class.");
				$this->$protocol = new $protocol($this);
				if(isset($this->$protocol->__requirements)) {
					foreach($this->$protocol->__requirements as $k => $v)
						$this->add($v);
					return $this->keyword("more");
				}
				return $this->keyword("ok");
			}
		}
		return $this->keyword("already_there");
	}
}
