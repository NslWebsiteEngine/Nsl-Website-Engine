<?php
class base {
    public $main;
    public $__requirements = [];
    public $author = "Danny Morabito";
    public $version = "1.0.0";
    
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
    
    function __destruct() {
        
    }
}