<?php
class validator extends base {
	function validateRpassword($password1, $password2) {
		return $password1 == $password2;   
	}
	function validatePassword($password, $options) {
		$defaults = [
			"length" => [8, 64],
			"letter" => true,
			"number" => true,
			"special" => false
		];
		$options = array_merge($defaults, $options);
		if(count($options["length"]) == 2) {
			if(strlen($password) < $options["length"][0])
				return false;
			if(strlen($password) > $options["length"][1])
				return false;
		}elseif(count($options["length"]) == 1){
			if(strlen($password) < $options["length"][0])
				return false;
		}else
			return false;
		if($options["letter"])
			if(!preg_match("/[A-Za-z]/", $password))
				return false;
		if($options["number"])
			if(!preg_match("/[0-9]/", $password))
				return false;
		if($options["special"])
			if(!preg_match("/[^A-Za-z0-9]/", $password))
				return false;
		return true;
	}
	function email($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	function url($url) {
		return filter_var($url, FILTER_VALIDATE_URL);
	}
	function onlyNumerics($string) {
		return preg_replace("/[^0-9]/", "", $string) == $string;
	}
	function onlyAlpha($string) {
		return preg_replace("/[^A-Za-z]/", "", $string) == $string;
	}
	function onlyAlphanumeric($string) {
		return preg_replace("/[^A-Za-z0-9]/", "", $string) == $string;
	}
	function onlyUppercase($string) {
		return strtoupper($string) == $string;
	}
	function onlyLowercase($string) {
		return strtolower($string) == $string;
	}
	function onlyNotAlpha($string) {
		return !$this->onlyAlpha($string);
	}
	function onlyNotNumeric($string) {
		return !$this->onlyNumerics($string);
	}
	function onlyNotAlphanumeric($string) {
		return !$this->onlyAlphanumeric($string);
	}
	function onlyCharsinarray($string, $array) {
		$string = str_split($string, 1);
		foreach($string as $v) {
			if(in_array($v, $array))
				return false;
		}
		return true;
	}
	function onlyHex($string) {
		return preg_replace("/[^A-Fa-f0-9]/", "", $string) == $string;
	}
	function onlyLowercaseHex($string) {
		return preg_replace("/[^a-f0-9]/", "", $string) == $string;
	}
	function onlyUppercaseHex($string) {
		return preg_replace("/[^A-F0-9]/", "", $string) == $string;
	}
	function maxlength($string, $max) {
		return strlen($string) > $max;
	}
	function minlength($string, $min) {
		return strlen($string) < $min;
	}
}
