<?php

/*
**********************************
*                                *
*       NSL Website Engine       *
*                                *
**********************************
* Copyright 2013 Danny Morabito, all rights reserved. 
* You can use the NSL Website Engine without any worries, just please leave 
* this small copyright message.
*/


class lib {
	public $pluginspath;
	public $defaults; 
	public $__usedprotocols = array();
	public $configuration = array();
	private $__removed = array();
	private $version = "0.3.0b";
	private $__composer = array();
	function __construct($configurationfile = null) {
		define("NSL_Website_Engine", $this->version);
		define("DS", DIRECTORY_SEPARATOR);
		header("X-Powered-By: NSL Website Engine/#".$this->version);
		$GLOBALS["NSLWebsiteEngine"] = &$this; // the NSlWebsiteEngine should be accessible globally
		$this->defaults = new stdClass;
		$this->pluginspath = $GLOBALS["NSLWebsiteEngine/pluginspath"] = $this->defaults->pluginspath = __DIR__.DS."classes".DS;
		$this->setpluginspath($this->pluginspath);
		$this->configuration = array(
			"plugins" => array(),
			"base" => array(
				"showerrors" => true
			),
		);
		include $this->pluginspath."base.php";
		if(!is_null($configurationfile)) {
			$this->configure($configurationfile);
			$this->add($this->configuration["plugins"]);
		}
	}
	function configure($file) {
		$newarray = json_decode(file_get_contents($file), true);
		return $this->configuration = array_merge($this->configuration, $newarray);
	}
	function __get($name) {
		if(!isset($this->$name)) {
			if(file_exists($this->pluginspath.DS.$name.".php"))
				$this->trigger_error("Class {$name} isn't loaded");
		}
	}
	function add($protocols) {
		if(is_array($protocols)) {
			$returns = array();
			foreach($protocols as $protocol)
				$returns[] = $this->_add($protocol);
			return $returns;
		}elseif(is_string($protocols))
			return $this->_add($protocols);
		return false;
	}
	function setpluginspath($path) {
		if(!is_dir($path))
			$path = $this->defaults->pluginspath;
		if(substr($path, -1) != DS)
			$path .= DS;
		$this->pluginspath = $path;
		$GLOBALS["NSLWebsiteEngine".DS."pluginspath"] = $this->pluginspath;
		return $path;
	}
	function set($protocol) { return $this->add($protocol); }
	function using($protocol) { return $this->add($protocol); }
	function protocol($protocol) {
		$protocol = strtolower($protocol);
		return isset($this->$protocol) ? $this->$protocol : null;
	}
	function destroy($protocol) {
		if(isset($this->__removed[$protocol]))
			$this->trigger_error("Protocol {$protocol} is already unset");
		if(!isset($this->$protocol))
			$this->trigger_error("The protocol {$protocol} isn't loaded");
		$removed = $this->__removed;
		$removed[$protocol] = $this->$protocol;
		$this->__removed = $removed;
		if(method_exists($this->$protocol, "__removed"))
			call_user_func([$this->$protocol, "__removed"]);
		$this->$protocol = null;
		return "ok";
	}
	function trigger_error($error = "", $editor = "") {
		if($this->configuration["base"]["showerrors"]) {
			if(isset($this->prettyerrors)) {
				if(!isset($this->__prettyobject)) {
					$ed = $this->__prettyobject = $this->prettyerrors->setTitle()->setArgs("NSL args", array(
						"Used Protocols" => $this->__usedprotocols,
						"Removed Protocols" => $this->__removed
					));
					if($editor != "")
						$ed->setEditor($editor);
					$ed->register();
				}
				throw new RuntimeException($error);
			}else
				die("<div class='nslerror'><b>NSL ERROR</b>: <i>{$error}</i></div>");
		}
	}
	function is_included($plugin_name, $truefalse = false) {
		// return true and false or the choosen name
		return isset($this->__usedprotocols[$plugin_name]) ? ($truefalse ? true : $this->__usedprotocols[$plugin_name]) : false;
	}
	function generate_composer($file = false) {
		$composer = array(
			"name" => "newsocialifecom/websiteengine",
			"description" => "The ultimative website engine of New Social Life",
			"require" => $this->__composer,
			"license" => "MIT",
			"authors" => array(
				array(
					"name" => "Danny Morabito",
					"email" => "NSL-Website-Engine@newsocialife.com"
				)
			),
			"minimum-stability" => "dev"
		);
		if($file) {
			file_put_contents("composer.json", json_encode($composer));
			return true;
		}else
			return json_encode($composer);
	}
	function _add($protocol) {
		// split protocol by .
		$protocol = explode(".", strtolower($protocol));
		// if protocol is an array containing two elements
		if(count($protocol) == 2) {
			// set name to the first element of the array ...
			$name = $protocol[0];
			// ... and protocol it'self to the second one
			$protocol = $protocol[1];
		}else
			// else set the name and the protocol to have the same name
			$protocol = $name = $protocol[0];
		// if the plugin $protocol isn't used
		if(!isset($this->__usedprotocols[$protocol]))
			// set the name of the new plugin as $name in the usedprotocols array
			$this->__usedprotocols[$protocol] = $name; 
		// when $name isn't already used
		if(!isset($this->$name)) {
			// if the plugin was added but removed
			if(isset($this->__removed[$protocol])) {
				// take back the removed protocol
				$this->$name = $this->__removed[$protocol];
				// remove it from the removed list
				unset($this->__removed[$protocol]);
				return "ok";
			}else{
				// set the filename variable to the absolute path of the to-add plugin
				$filename = $this->pluginspath.strtolower($protocol).".php";
				// if the plugin exists
				if(file_exists($filename))
					// include it but check that it's not already included
					include_once($filename);
				else {
					// show an error to the user
					$this->trigger_error("Unable to find {$protocol} plugin.");
					return "not ok";
				}
				// add the protocol to the variable
				$this->$name = new $protocol($this);
				// does it have a return value (special plugins only)? If so then set the plugin to the return value
				$this->$name = gettype($this->$name->__construct($this)) == "NULL" ? $this->$name : $this->$name->__construct($this);
				// does the plugin depend on other plugins?
				if(isset($this->$name->__requirements)) {
					// does the plugin require some specific php settings?
					if(isset($this->$name->__requirements["php"])) {
						// set the variable $php_reqs to the requirements it's self and then unset it
						$php_reqs = $this->$name->__requirements["php"];
						unset($this->$name->__requirements["php"]);
						// go through all the requirements
						foreach($php_reqs as $req => $value) {
							// All the keywords should be converted to lowercase
							$req = strtolower($req);
							// if the user requires a specific minimum or maximum version the check it and report if necessary
							if($req == "version" || $req == "version_min") {
								if(!version_compare(PHP_VERSION, $value, ">=")) {
									$this->trigger_error("The plugin {$protocol} isn't compatible with your php version ( >= {$value} )");
									return "not ok";
								}
							}elseif($req == "version_max") {
								if(!version_compare(PHP_VERSION, $value, "<=")) {
									$this->trigger_error("The plugin {$protocol} isn't compatible with your php version ( <= {$value} )");
									return "not ok";
								}
							}else{
								// if it's not a version requirement check if the user has the required function and if not report an error
								if(!function_exists($value) || !is_callable($value)) {
									$this->trigger_error("The plugin {$protocol} requires the function ({$value}) which isn't enabled in your server");
									return "not ok";
								}
							}
						}
					}
					// add all the other requirements
					$this->add($this->$name->__requirements);
					// if the are some required composer packages
					if(isset($this->$name->__composer_requirements))
						// then add 'em to the composer packages array
						$this->__composer = array_merge($this->__composer, $this->$name->__composer_requirements);
					return "ok";
				}
			}
		}else{
			// show the error
			$this->trigger_error("The plugin name {$name} is already set, if you want to use this one, please set another name for it");
			return "already_there";
		}
	}
	function getVersion() {
		return $this->version;
	}
}
