<?php
class i18n extends base {
	protected $__requirements = array("utils");
	public $language;
	function set($language) {
		$language = str_replace(chr(0), "", strtolower($language));
		$language = preg_replace("/[^A-Za-z\_]/", "", $language);
		if(strlen($language) != 2 && strlen($language) != 5)
			$this->main->trigger_error("The language {$language} contains an invalid name");
		if(is_file("languages".DS."{$language}.json")) {
			$this->language = $this->main->utils->json_decode_file("languages".DS."{$language}.json");
		}else{
			$tmp = current(explode("_", $language));
			if(is_file("languages".DS."{$tmp}.json"))
				$this->language = $this->main->utils->json_decode_file("languages".DS."{$tmp}.json");
			else
				$this->main->trigger_error("The file {$language}.json couldn't be found");
		}
		return true;
	}
	function getTranslation($text, $args) {
		if(isset($this->language->$text))
			return $this->sprintf($this->language->$text, $args);
		else
			return $this->sprintf($text, $args);
	}
	function sprintf($text, $args = null) {
		if(is_array($args)) {
			foreach($args as $k => $v)
				$text = str_replace("%{$k}%", $v, $text);
		}elseif(is_string($args))
			$text = str_replace("%%%", $args, $text);
		return $text;
	}
}