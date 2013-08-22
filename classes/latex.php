<?php
include __DIR__.DS."..".DS."vendor".DS".gregwar".DS."tex2png".DS."Gregwar".DS."Tex2png".DS."Tex2png.php";
class latex extends base {
	public $version = "1.0.2";
	public $__composer_requirements = array("gregwar/tex2png" => "*");

	function __construct(&$main) {
		parent::__construct($main);
		$this->compiler = new Gregwar\Tex2png\Tex2png\Tex2png("");
		return $this;
	}
	function add($package) {
		$this->compiler->packages[] = $package;
		return $this;
	}
	function generate($latex, $density = 155) {
		return $this->compiler->create($latex, $density)->
			generate()->
			getFile();
	}
	function compile_from_file($file, $density = 155) {
		return $this->generate(file_get_contents($file), $density);
	}
}