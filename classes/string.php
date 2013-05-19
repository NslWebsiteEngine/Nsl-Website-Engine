<?php
class string extends base {
	function __construct(&$main, $string = "") {
		parent::__construct($main);
		$this->content = $string;
		return $this;
	}
	function __set($name, $value) {
		$this->content = $value;
	}
	function __tostring() {
		return (string)$this->content;
	}
	function __invoke($content) {
		$this->content = $content;
		return new string($this->main, $this->content);
	}
	function __call($name, $args) {
		$allowed = [
			"addcslashes" => 0,
			"addslashes" => 0,
			"chop" => 0,
			"chunk_split" => 0,
			"convert_uudecode" => 0,
			"convert_uuencode" => 0,
			"count_chars" => 0,
			"crc32" => 0,
			"crypt" => 0,
			"echo" => 0,
			"fprintf" => 1,
			"hebrev" => 0,
			"hebrevc" => 0,
			"html_entity_decode" => 0,
			"htmlentities" => 0,
			"htmlspecialchars_decode" => 0,
			"htmlspecialchars" => 0,
			"lcfirst" => 0,
			"levenshtein" => 0,
			"ltrim" => 0,
			"md5" => 0,
			"metaphone" => 0,
			"money_format" => 0,
			"nl2br" => 0,
			"ord" => 0,
			"parse_str" => 0,
			"print" => 0,
			"printf" => 0,
			"quoted_printable_decode" => 0,
			"quoted_printable_encode" => 0,
			"quotemeta" => 0,
			"rtrim" => 0,
			"sha1" => 0,
			"similar_text" => 0,
			"soundex" => 0,
			"sprintf" => 0,
			"sscanf" => 0,
			"str_getcsv" => 0,
			"str_ireplace" => 2,
			"str_pad" => 0,
			"str_repeat" => 0,
			"str_replace" => 2,
			"str_rot13" => 0,
			"str_shuffle" => 0,
			"str_split" => 0,
			"str_word_count" => 0,
			"strcasecmp" => 0,
			"strchr" => 0,
			"strcmp" => 0,
			"strcoll" => 0,
			"strcspn" => 0,
			"strip_tags" => 0,
			"stripcslashes" => 0,
			"stripslashes" => 0,
			"stristr" => 0,
			"strlen" => 0,
			"strnatcasecmp" => 0,
			"strnatcmp" => 0,
			"strncasecmp" => 0,
			"strncmp" => 0,
			"strpbrk" => 0,
			"strpos" => 0,
			"strrchr" => 0,
			"strrev" => 0,
			"strripos" => 0,
			"strrpos" => 0,
			"strspn" => 0,
			"strstr" => 0,
			"strtolower" => 0,
			"strtoupper" => 0,
			"strtr" => 0,
			"substr_compare" => 0,
			"substr_count" => 0,
			"substr" => 0,
			"trim" => 0,
			"ucfirst" => 0,
			"ucwords" => 0,
			"vfprintf" => 1,
			"vprintf" => 0,
			"vsprintf" => 0,
			"wordwrap" => 0,
			"preg_replace" => 2,
			"preg_replace_callback" => 2,
			"preg_split" => 1
		];
		if(isset($allowed[$name])) {
			array_splice($args, $allowed[$name], 0, [$this->content]);
			return $this->string_method($name, $args);
		}else
			$this->main->trigger_error("The called method is not allowed for strings, if you want to have it, please report it in the Github's issues page.");
	}
	function string_method($method, $args) {
		$ret = call_user_func_array($method, $args);
		if(is_string($ret))
			return new string($this->main, $ret);
		return $ret;
	}
	function replace($search, $replace) {
		return new string($this->main, str_replace($search, $replace, $this->content));
	}
}