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
}