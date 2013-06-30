<?php
class base {
	public $main;
	protected $__requirements = [];
	public $author = "Danny Morabito";
	public $version = "1.0.0";
	public $edits = [];
	
	function __construct(&$main) {
		$this->main = &$main;
	}
	
	function getAuthor() {
		return $this->author;
	}
	
	function getVersion() {
		if(count(explode(".", $this->version)) != 3 && substr($this->version, -1) != "b") 
			$this->version .= ".0";
		return $this->version;
	}
	function returnMe() {
        	return $this;
        }
        function getEdits() {
        	return $this->edits;
        }
        function configuration($keyword) {
        	if(isset($this->main->configuration[__CLASS__][$keyword]))
        		return $this->main->configuration[__CLASS__][$keyword];
        	return false;
        }
	
	function __destruct() {
		
	}
}
