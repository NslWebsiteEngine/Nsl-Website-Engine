<?php
include_once __DIR__.DS."..".DS."vendor".DS."autoload.php";
class less extends base {
	public $version = "1.0.1";
	public $__composer_requirements = array("leafo/lessphp" => "0.3.*@dev");

    function __construct(&$main) {
        parent::__construct($main);
        $this->less = new lessc;
    }
    function setVars($vars) {
        return $this->less->setVariables($vars);
    }
    function compile($file, $output) {
        return $this->less->checkedCompile($file, $output);
    }
    function compileString($string) {
        return $this->less->compile($string);
    }
    function compileFile($file) {
        return $this->less->compileFile($file);
    }
}
