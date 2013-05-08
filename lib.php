<?php
class lib {
	protected $__keywords__;
	protected $__keywords_counter__ = 0xF0;
	protected $__removed = [];
	protected $pluginspath;
	protected $defaults;
	protected $__usedprotocols = [];
	function __construct() {
		$this->keyword("more");
		$this->keyword("ok");
		$this->keyword("already_there");
		$this->defaults = new stdClass;
		$this->pluginspath = $this->defaults->pluginspath = __DIR__."/classes/";
		include $this->pluginspath."base.php";
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
	function setpluginspath($path) {
		if(!is_dir($path))
			$path = $this->defaults->pluginspath;
		if(substr($path, -1) != "/")
			$path .= "/";
		$this->pluginspath = $path;
		return $path;
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
			$this->trigger_error("The protocol {$protocol} isn't loaded");
		$removed = $this->__removed;
		$removed[$protocol] = $this->$protocol;
		$this->__removed = $removed;
		if(method_exists($this->$protocol, "__removed"))
			call_user_func([$this->$protocol, "__removed"]);
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
	function trigger_error($error = "") {
		if(isset($this->prettyerrors)) {
			if(!isset($this->__prettyobject))
			$this->__prettyobject = $this->prettyerrors->setTitle()->setArgs("NSL args", [
				"Used Protocols" => $this->__usedprotocols,
				"Removed Protocols" => $this->__removed
			])->register();
			throw new RuntimeException($error);
		}else
			die("<div class='nslerror'><b>NSL ERROR</b>: <i>{$error}</i></div>");
	}
	function _add($protocol) {
		$protocol = strtolower($protocol);
		if(!isset($this->$protocol)) {
			$this->__usedprotocols[] = $protocol;
			if(isset($this->__removed[$protocol])) {
				$this->$protocol = $this->__removed[$protocol];
				unset($this->__removed[$protocol]);
				return $this->keyword("ok");
			} else {
				$name = $this->pluginspath.strtolower($protocol).".php";
				if(file_exists($name))
					include_once $name;
				else
					$this->trigger_error("Unable to find {$protocol} class.");
				$this->$protocol = new $protocol($this);
				if(isset($this->$protocol->__requirements)) {
					$this->add($this->$protocol->__requirements);
					return $this->keyword("more");
				}
				return $this->keyword("ok");
			}
		}
		return $this->keyword("already_there");
	}
}
