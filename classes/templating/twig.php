<?php
include_once __DIR__.DS."..".DS."..".DS."vendor".DS."autoload.php";
class twig extends base {
	private $twig;
	public $index;
	public $views;
	public $__requirements_composer = array("twig/twig" => "*");
	public $version = "1.0.1";
	
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
		$this->i18n = $this->main->is_included("i18n");
	}
	function setLanguage($lang) {
		$lib = $this->main;
		$i18n = $this->i18n;
		$lib->$i18n->set($lang);
		$this->addFunction("i18n", function($text, $args = []) use($lib, $i18n) {
			return $lib->$i18n->getTranslation($text, $args);
		});
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