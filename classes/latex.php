<?php
include "../vendor/gregwar/tex2png/Gregwar/Tex2png/Tex2png.php";
class latex extends base {
	function __construct(&$main) {
		parent::__construct($main);
		$this->compiler = new Gregwar\Tex2png\Tex2png\Tex2png("");
		return $this;
	}
	function add($package) {
		$this->compiler->packages[] = $package;
		return $this;
	}
	function generate($latex) {
		return $this->compiler->create($latex)->
			generate()->
			getFile();
	}
	function compile_from_file($file) {
		return $this->generate(file_get_contents($file));
	}
}