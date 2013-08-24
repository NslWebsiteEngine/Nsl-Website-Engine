<?php
class base {
    public $main;
	protected $__requirements = array();
	public $author = "Danny Morabito";
	public $version = "1.0.0";
    public $edits = array();
    public $__composer_requirements = array();
	
	function __construct(&$main) {
		$this->main = &$main;
	}
    
    function _() {
        return $this->main;
    }
	
	function getAuthor() {
		return $this->author;
	}
	
	function getVersion() { // major.minor[.build[.revision]]
		list($major, $minor, $build, $revision) = explode(".", $this->version);
		if(!!!$build)
			return implode(".", array($major, $minor, 0));
		if(!!!$revision)
			return implode(".", array($major, $minor, $build));
		return implode(".", array($major, $minor, $build, $revision));
	}

    function returnMe() {
        return $this;
    }

    function getEdits() {
        return $this->edits;
    }

    function configuration($keyword, $class) {
        if(isset($this->main->configuration[$class][$keyword]))
            return $this->main->configuration[$class][$keyword];
        return false;
    }
	
	function __destruct() {
		
	}
}
