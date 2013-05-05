<?php
/*
VERY MUCH THANKS TO Daniel.L.Wood@Gmail.com for the basic class.
*/
class typehint extends base {
	static private $Typehints = array(
		'bool' => 'is_bool',
		'int' => 'is_int',
		'boolean' => 'is_bool',
		'integer' => 'is_int',
		'double' => 'is_double',
		'float'	 => 'is_float',
		'string' => 'is_string',
		'resource' => 'is_resource',
		'null' => 'is_null',
		'void' => 'is_null'
	);
	function __construct(&$main) {
        parent::__construct($main);
		set_error_handler(__CLASS__.'::handleTypehint');
		self::$Typehints['mixed'] = function() { return true; };
		self::$Typehints['number'] = function($x) { return is_int($x) || is_float($x) || is_double($x); };
		return true;
	}
	private static function getTypehintedArgument($ThBackTrace, $ThFunction, $ThArgIndex, &$ThArgValue) {
		foreach($ThBackTrace as $ThTrace) {
			if (isset($ThTrace['function']) && $ThTrace['function'] == $ThFunction) {
				$ThArgValue = $ThTrace['args'][$ThArgIndex - 1];
				return true;
			}
		}
		return false;
	}
	public static function handleTypehint($ErrLevel, $ErrMessage) {
		if($ErrLevel == E_RECOVERABLE_ERROR) {
			if(preg_match("/^Argument (\d)+ passed to (?:(\w+)::)?(\w+)\(\) must be an instance of (\w+), (\w+) given/", $ErrMessage, $ErrMatches)) {
				list($ErrMatch, $ThArgIndex, $ThClass, $ThFunction, $ThHint, $ThType) = $ErrMatches;
				if(isset(self::$Typehints[$ThHint])) {
					$ThBacktrace = debug_backtrace();
					$ThArgValue  = null;
					if(self::getTypehintedArgument($ThBacktrace, $ThFunction, $ThArgIndex, $ThArgValue))
						if(call_user_func(self::$Typehints[$ThHint], $ThArgValue))
							return true;
				}
			}
		}
		return false;
	}
}