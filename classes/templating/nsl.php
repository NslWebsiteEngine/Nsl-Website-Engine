<?php 
include_once __DIR__.DS."twig.php";
class nsl extends twig {
	function render($file, $options = array(), $dotnsl = true) {
		$string = file_get_contents($this->views.DS.$file.($dotnsl ? ".nsl" : ""));
		$string = preg_replace([
			"/<(nsl:)?get(\-)?>([^\<]+)<\/(nsl\:)?get>/",
			"/<(nsl:)?if condition=\"(.*)\">/",
			"/<(nsl:)?for el=\"([A-Za-z0-9\_]+)\" in=\"([A-Za-z0-9\_]+)\">/",
			"/<(nsl:)?filter (name|parameter|filter|use)=\"([A-Za-z0-9\_]+)\">/",
			"/<(nsl:)?include file\=\"([^\"]+)\"(\s\/)?>/",
			"/<(nsl:)?block name=\"([^\"]+)\">/",
			"/<(nsl:)?set name=\"([A-Za-z0-9\_]+)\">/",
			"/<(nsl:)?spaceless>/",
			"/<\/(nsl:)?if>/",
			"/<\/(nsl:)?else>/",
			"/<\/(nsl:)?for>/",
			"/<\/(nsl:)?autoescape>/",
			"/<\/(nsl:)?filter>/",
			"/<\/(nsl:)?block>/",
			"/<\/(nsl:)?set>/",
			"/<\/(nsl:)?spaceless>/",
		], [
			'{{$2 $3 }}',
			'{% if $2 %}',
			'{% for $2 in $3 %}',
			"{% filter $3 %}",
			"{% include $2 %}",
			"{% block $2 %}",
			"{% set $2 = ",
			"{% spaceless %}",
			"{% endif %}",
			"{% else %}",
			"{% endfor %}",
			"{% endautoescape %}",
			"{% endfilter %}",
			"{% endblock %}",
			" %}",
			"{% endspaceless %}"
		], $string);
		echo $string;
		return parent::renderString($string, $options);
	}
}
?>