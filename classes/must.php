<?php
class must extends base {
	function get($params, $callback = null, $callbackelse = null) {
		$this->handle($_GET, $params, $callback, $callbackelse);
	}
	function post($params, $callback = null, $callbackelse = null) {
		$this->handle($_POST, $params, $callback, $callbackelse);
	}
	function cookie($params, $callback = null, $callbackelse = null) {
		$this->handle($_COOKIE, $params, $callback, $callbackelse);
	}
	function handle($arr, $params, $callback = null, $callbackelse = null) {
		if(is_null($callback))
			$callback = function($name) {
				die("NSL ERROR: Parameter {$name} is required but isn't set");
			};
		if(is_null($callbackelse))
			$callbackelse = function() {
				return true;
			};
		if(is_array($params)) {
			foreach($params as $param) {
				if(!isset($arr[$param]) || strlen($arr[$param]) < 1)
					return $callback($param);
			}
		}else
			if(!isset($arr[$params]) || strlen($arr[$params]) < 1)
				return $callback($params);
	}
}