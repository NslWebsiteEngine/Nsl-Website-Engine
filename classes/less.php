<?php
include_once __DIR__."/../vendor/autoload.php";
class less extends base {
    function __construct() {
        parent::__construct();
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