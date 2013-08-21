<?php
include_once __DIR__.DS."..".DS."vendor".DS."autoload.php";
class templating extends base {
	private $twig;
    public $index;
    public $views;
    
	function __construct(&$main) {
        parent::__construct($main);
        if($this->configuration("index", "templating"))
            $this->setIndex($this->configuration("index"), "templating");
        else
            $this->setIndex("index.twig");
        if($this->configuration("views", "templating"))
            $this->setViews($this->configuration("views", "templating"));
        else
            $this->setViews("views");
        if($this->configuration("cache", "templating"))
            $twigconfig = array(
            	'cache' => $this->configuration("cache", "templating")
	    	);
        else
            $twigconfig = array();
        $this->twig = new Twig_Environment(new Twig_Loader_Filesystem(realpath($this->views.DS), $twigconfig));
	}
    function setIndex($file) {
        $this->index = $file;
        return $this;
    }
    function setViews($views) {
        $this->views = $views;
        return $this;
    }
	function addFilter($name, $callback, $options = array()) {
		$this->twig->addFilter(new Twig_SimpleFilter($name, $callback, $options));
        return $this;
	}
	function addFunction($name, $callback, $options = array()) {
		$this->twig->addFunction(new Twig_SimpleFunction($name, $callback, $options));
        return $this;
	}
	function render($file, $options = array()) {
		if(!isset($options["filename"])) {
			$options = array_merge($options, array("filename" => $file.".twig"));
			return $this->twig->render($this->index, $options);
		}else{
			$page = $options["filename"];
			$options = array_merge($options, array("filename" => $file.".twig"));
			return $this->twig->render($page.".twig", $options);
		}
	}
}
