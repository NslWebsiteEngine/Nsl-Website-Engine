<?php
class utils {
    function json_decode_file($file) {
    	return json_decode(file_get_contents("{$file}"));
	}
    function toObject($array) {
    	$obj = new stdClass();
		foreach ($array as $key => $val)
			$obj->$key = is_array($val) ? self::toObject($val) : $val;
		return $obj;
	}
	function toArray($d) {
		if(is_object($d))
			$d = get_object_vars($d);
		if(is_array($d))
			return array_map(__FUNCTION__, $d);
		else
			return $d;
	}
}