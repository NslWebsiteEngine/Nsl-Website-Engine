<?php
class utils {
    function is_json($json) {
        if(substr($json, 0, 1) != "{" && substr($json, 0, 1) != "[" && substr($json, -1) != "}" && substr($json, -1) != "]")
            return false;
        $json = json_decode($json);
        return json_last_error() == JSON_ERROR_NONE;
    }
    function json_decode_file($file) {
    	return json_decode(file_get_contents("{$file}"));
	}
    function usenslargs() {
        ini_set('arg_separator.output',';');
    }
    function json_encode_file($file, $contents) {
        if(!$this->is_json($contents))
            return file_put_contents($file, json_encode($contents));
        return file_put_contents($file, $contents);
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