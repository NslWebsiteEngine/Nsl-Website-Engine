<?php
class router {	
	function route($method, $path, $function) {
		return respond($method, "/".$path, $function);
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
		return respond($path, $function);
	}
	function __destruct() {
		dispatch();
	}
}