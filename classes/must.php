<?php
class must {
	function get($params, $callback) {
		$this->handle($_GET, $params, $callback);
	}
	function post($params, $callback) {
		$this->handle($_POST, $params, $callback);
	}
	function cookie($params, $callback) {
		$this->handle($_COOKIE, $params, $callback);
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
				if(!isset($arr[$param]))
					$callback($param);
			}
		}else
			if(!isset($arr[$params]))
				$callback($params);
	}
}
