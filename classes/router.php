<?php
class router extends base {
    public $routes;
    public $errors;
    public $with;
    
    function __construct(&$main) {
        parent::__construct($main);
        $this->error(404, function() {
            echo "The requested page could not be found";
		});
        $this->error(405, function() {
            echo "The requested method isn't available for the requested page.";
        });
        $this->with("/");
        if(isset($_SERVER["PATH_INFO"]))
            $this->setUrl($_SERVER["PATH_INFO"]);
    }
	function route($method, $path, $function) {
		if(substr($path, 0, strlen($this->with)) != $this->with)
			$path = $this->with.$path;
		$path = "/^".str_replace('/', '\/', $path)."$/";
		$this->routes[$path][strtoupper($method)] = $function;
		return $this;
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
    function head($path, $function) {
        return $this->route("HEAD", $path, $function);
    }
	function all($path, $function) {
		return $this->route("ALL", $path, $function);
	}
	function setURL($u) {
		$this->url = $u;
        return $this;
	}
    function with($namespace) {
        if(substr($namespace, 0, 1) != "/")
            $namespace = "/".$namespace;
        $this->with = $namespace;
        return $this;
    }
    function error($code = 200, $function = null) {
        if(is_null($function)) {
            if(isset($this->errors[$code]))
                return $this->errors[$code];
            else
                return null;
        }else
            return $this->errors[$code] = $function;
    }
	function getMethod() {
		return strtoupper(
			isset($_REQUEST["__method__"]) ? $_REQUEST["__method__"] : (
				isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : (
					isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET'
				)
			)
		);
	}
	function __destruct() {
        parent::__destruct();
		$url = isset($this->url) ? $this->url : "/";
		uksort($this->routes, function($a, $b) {
			return strlen($a) > strlen($b) ? $a : $b;
		});
		foreach($this->routes as $pattern => $array) {
			if(preg_match($pattern, $url, $params)) {
                if(isset($array["ALL"]))
                    $mtd = "ALL";
                elseif(isset($array[$this->getMethod()]))
                    $mtd = $this->getMethod();
                else
                    return call_user_func_array($this->error(405), []);
                array_shift($params);
                return call_user_func_array($array[$mtd], array_values($params));
			}
		}
		return call_user_func_array($this->error(404), []);
	}
}
