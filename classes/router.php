<?php
class router extends base {
	function route($method, $path, $function) {
		if(substr($path, 0, 1) != "/")
			$path = "/".$path;
		if($method == "404" || $path == "404")
			$this->routes["/404"] = [$method, $function];
		$path = "/".str_replace('/', '\/', $path)."/";
		$this->routes[$path] = [$method, $function];
		return $this->routes;
	}
	function get($path, $function) {
		return $this->route("GET", $path, $function);
	}
	function post($path, $function) {
		return $this->route("POST", $path, $function);
	}
	function put($path, $function) {
		return $this->route("PUT", $path, $function);
	}
	function delete($path, $function) {
		return $this->route("DELETE", $path, $function);
	}
	function all($path, $function) {
		return $this->route("ALL", $path, $function);
	}
	function setURL($u) {
		$this->url = $u;
	}
	function getMethod() {
		return (
			isset($_REQUEST["__method__"]) ? $_REQUEST["__method__"] : (
				isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : (
					isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET'
				)
			)
		);
	}
	function __destruct() {
        parent::__desturct();
		if(!isset($this->routes["/\/404/"]))
			$this->routes["/\/404/"] = ["ALL", function() {
				echo "The requested page could not be found";
			}];
		$url = isset($this->url) ? $this->url : "/";
		uksort($this->routes, function($a, $b) {
			return strlen($a) > strlen($b) ? $a : $b;
		});
		foreach($this->routes as $pattern => $args) {
			if(preg_match($pattern, $url, $params)) {
				if(strtoupper($args[0]) == strtoupper($this->getMethod()) || strtoupper($args[0]) == "ALL") {
					array_shift($params);
					return call_user_func_array($args[1], array_values($params));
				}else
					return call_user_func_array($this->routes["/\/404/"][1], []);
			}
		}
		return call_user_func_array($this->routes["/\/404/"][1], []);
	}
}
