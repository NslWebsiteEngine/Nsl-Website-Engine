<?php
class plural extends base {
	function __get($string) {
		if(substr($string, -1) == "y")
			return substr($string, 0, -1)."ies";
		$special = array(
			"information",
			"money",
			"news"
		);
		if(in_array($string, $special))
			return $string;
		$irregulars = array(
			'/(matr|vert|ind)(ix|ex)$/i'	=> '\1ices',
			'/(ss|sh|ch|x|z)$/i'			=> '\1es',
			'/([^aeiou])o$/i'			   => '\1oes',
			'/([^aeiou]|qu)y$/i'			=> '\1ies',
			'/sis$/i'					   => 'ses',
			'/(m|l)ouse$/i'				 => '\1ice',
			'/(t|i)um$/i'				   => '\1a',
			'/([li])fe?$/i'				 => '\1ves',
			'/(vir|syllab)us$/i'			=> '\1i',
			'/(ax|test)is$/i'			   => '\1es',
			'/([a-rt-z])$/i'				=> '\1s'
		);
		$string2 = preg_replace(array_keys($irregulars), array_values($irregulars), $string);
		if($string2 != $string)
			return $string2;
		return $string."s";
	}   
}