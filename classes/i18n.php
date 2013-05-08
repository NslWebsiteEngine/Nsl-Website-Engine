<?php
class i18n extends base {
    protected $__requirements = ["utils"];
	public $languages;
	function addLanguage($language) {
		$language = str_replace(chr(0), "", strtolower($language));
		$language = preg_replace("/[^A-Za-z\_]/", "", $language);
		if(strlen($language) != 2 && strlen($language) != 5)
			$this->main->trigger_error("The language {$language} contains an invalid name");
		if(isset($this->languages[$language]))
			$this->main->trigger_error("The language {$language} is already loaded");
		if(is_file("languages/{$language}.json")) {
			$this->languages[$language] = $this->json_decode_file("languages/{$language}.json");
		}else{
			$tmp = current(explode("_", $language));
			if(is_file("languages/{$tmp}.json"))
				$this->languages[$language] = $this->main->json_decode_file("languages/{$tmp}.json");
			else
				$this->main->trigger_error("The file {$language}.json couldn't be found");
		}
		return true;
	}
	function removeLanguage($language) {
		unset($this->languages[$langunage]);
	}
	function setLang($lang) {
		if(!isset($this->languages[$language]))
			$this->main->trigger_error("The languages {$lang} isn't loaded");
		$this->lang = $lang;
	}
	function getTranslation($text, $args) {
		if(isset($this->lang[$text]))
			return $this->sprintf($this->lang[$text], $args);
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
