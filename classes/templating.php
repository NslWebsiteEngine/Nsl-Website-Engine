<?php
include_once __DIR__.DS."..".DS."vendor".DS."autoload.php";
class templating extends base {
	private $twig;
	function __construct(&$main) {
        parent::__construct($main);
		$this->twig = new Twig_Environment(new Twig_Loader_Filesystem(realpath('views'.DS)), [
			//'cache' => '/path/to/compilation_cache'
		]);
	}
	function addFilter($name, $callback, $options = []) {
		return $this->twig->addFilter(new Twig_SimpleFilter($name, $callback, $options));
	}
	function addFunction($name, $callback, $options = []) {
		return $this->twig->addFunction(new Twig_SimpleFunction($name, $callback, $options));
	}
	function render($file, $options = []) {
		if(!isset($options["filename"])) {
			$options = array_merge($options, ["filename" => $file.".twig"]);
			return $this->twig->render("index.twig", $options);
		}else{
			$page = $options["filename"];
			$options = array_merge($options, ["filename" => $file.".twig"]);
			return $this->twig->render($page.".twig", $options);
		}
	}
}
