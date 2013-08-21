<?php
class oarray extends base {
	function __construct(&$main, $oarray = "") {
		parent::__construct($main);
		$this->content = $oarray;
		return $this;
	}
	function __set($name, $value) {
		$this->content = $value;
	}
	function __invoke($content) {
		$this->content = $content;
		return new oarray($this->main, $this->content);
	}
	function __call($name, $args) {
		$allowed = array(
			"array_keys" => 0,
			"array_values" => 0,
			"array_merge" => 0,
			"array_change_key_case" => 0,
			"array_chunk" => 0,
			"array_column" => 0,
			"array_combine" => 1, // don't know if make it for keys or for values……… i choose values
			"array_count_values" => 0,
			"array_diff_assoc" => 0,
			"array_diff_key" => 0,
			"array_diff_uassoc" => 0,
			"array_intersect" => 0,
			"array_key_exists" => 0,
			"array_map" => 1,
			"array_merge_recursive" => 0,
			"array_multisort" => 0,
			"array_pad" => 0,
			"array_pop" => 0,
			"array_product" => 0,
			"array_push" => 0,
			"array_rand" => 0,
			"array_reduce" => 0,
			"array_replace_recursive" => 0,
			"array_replace" => 0,
			"array_reverse" => 0,
			"array_search" => 1,
			"array_shift" => 0,
			"array_slice" => 0,
			"array_splice" => 0,
			"array_sum" => 0,
			"array_udiff_assoc" => 0,
			"array_udiff_uassoc" => 0,
			"array_udiff" => 0,
			"array_uintersect_assoc" => 0,
			"array_uintersect_uassoc" => 0,
			"array_uintersect" => 0,
			"array_unique" => 0,
			"array_unshift" => 0,
			"array_values" => 0,
			"array_walk_recursive" => 0,
			"array_walk" => 0,
			"arsort" => 0,
			"asort" => 0,
			"compact" => 0,
			"count" => 0,
			"current" => 0,
			"each" => 0,
			"end" => 0,
			"extract" => 0,
			"in_array" => 1,
			"key" => 0,
			"krsort" => 0,
			"ksort" => 0,
			/* "list" => ?????, */ // cannot be implemented, sorry :)
			"natcasesort" => 0,
			"natsort" => 0,
			"next" => 0,
			"pos" => 0,
			"prev" => 0,
			"reset" => 0,
			"shuffle" => 0,
			"rsort" => 0,
			"sizeof" => 0,
			"sort" => 0,
			"uasort" => 0,
			"uksort" => 0,
			"usort" => 0
		);
		if(isset($allowed[$name])) {
			array_splice($args, $allowed[$name], 0, [$this->content]);
			return $this->array_method($name, $args);
		}else{
			$this->main->trigger_error("The called method is not allowed for OOP Arrays, if you want to have it, please report it in the Github's issues page.");
		}
	}
	function array_method($method, $args) {
		if(!function_exists($method))
			$method = "array_".$method;
		if(!function_exists($method))
			$this->main->trigger_error("The function called doesn't exist… could be a bug? Please report to the github repo")
		$ret = call_user_func_array($method, $args);
		if(is_array($ret))
			return new oarray($this->main, $ret);
		return $ret;
	}
}