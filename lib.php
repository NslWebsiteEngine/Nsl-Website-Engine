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
	public $__keywords__;
	public $__keywords_counter__ = 0xF0;
	public $pluginspath;
	public $defaults; 
	public $__usedprotocols = array();
	public $configuration = array();
	private $__removed = array();
	private $version = "0.2.0b";
	private $__composer = array();
	function __construct($configurationfile = null) {
		$this->keyword("ok");
		$this->keyword("already_there");
		$this->keyword("no_plugin_namespace");
		$this->keyword("NSL Website Engine", $this->version);
		define("NSL_Website_Engine", $this->version);
		define("DS", DIRECTORY_SEPARATOR);
		header("X-Powered-By: NSL Website Engine/#".$this->keyword("NSL Website Engine"));
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
		return $this->keyword("ok");
	}
	function keyword($k, $v = null) {
		if(!isset($this->__keywords__[$k])) {
			if(is_null($v))
				$this->__keywords__[$k] = $v;
			else {
				$this->__keywords__[$k] = $this->__keywords_counter__;
				$this->__keywords_counter__ += strlen($k);
			}
		}
		return dechex($this->__keywords__[$k]);
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
			"minimum-stability": "dev"
		);
		if($file) {
			file_put_contents("composer.json", json_encode($composer));
			return true;
		}else
			return json_encode($composer);
	}
	function _add($protocol) {
		if(preg_replace("/([a-z])([A-Z])/", "$1/$2", $protocol) != $protocol)
			return $this->_folder_add($protocol);
		$protocol = strtolower($protocol);
		if(!isset($this->$protocol)) {
			$this->__usedprotocols[] = $protocol;
			if(isset($this->__removed[$protocol])) {
				$this->$protocol = $this->__removed[$protocol];
				unset($this->__removed[$protocol]);
				return $this->keyword("ok");
			} else {
				$name = $this->pluginspath.strtolower($protocol).".php";
				if(file_exists($name))
					include_once $name;
				else
					$this->trigger_error("Unable to find {$protocol} class.");
				$this->$protocol = new $protocol($this);
				$this->$protocol = gettype($this->$protocol->__construct($this)) == "NULL" ? $this->$protocol : $this->$protocol->__construct($this);
				if(isset($this->$protocol->__requirements))
					$this->add($this->$protocol->__requirements);
				if(isset($this->$protocol->__composer_requirements))
					$this->__composer = array_merge($this->__composer, $this->$protocol->__composer_requirements);
				return $this->keyword("ok");
			}
		}
		return $this->keyword("already_there");
	}
	function _folder_add($protocol) {
		$original = $protocol;
		$protocol = preg_replace("/([a-z])([A-Z])/", "$1/$2", $protocol);
		$protocol = strtolower($protocol);
		$protocol = explode("/", $protocol);
		if(is_dir($this->pluginspath.$protocol[0])) {
			$dirname = array_shift($protocol);
			$dir = $this->pluginspath.$dirname;
			if(count($protocol) > 1)
				$this->trigger_error("Sub sub level plugins are not implemented");
			if(file_exists($dir.DS."base.php")) // every sub level can have a base.php
				include_once $dir.DS."base.php";
			$protocol = strtolower(array_shift($protocol));
			if(!isset($this->$dirname->$protocol)) {
				$this->__usedprotocols[$dirname][] = $protocol;
				$this->__usedprotocols[] = $dirname.ucfirst(strtolower($protocol));
				if(isset($this->__removed[$dirname][$protocol])) {
					$this->$protocol = $this->__removed[$dirname][$protocol];
					unset($this->__removed[$dirname][$protocol]);
					return $this->keyword("ok");
				} else {
					$name = $dir.DS.strtolower($protocol).".php";
					if(file_exists($name))
						include_once $name;
					else
						$this->trigger_error("Unable to find {$dirname}{$protocol} class.");
					$protocolname = $dirname.ucfirst(strtolower($protocol));
					if(!isset($this->$dirname))
						$this->$dirname = new stdClass;
					if(class_exists($protocolname))
						$this->$dirname->$protocol = new $protocolname($this);
					else {
						$onlyprot = strtolower(substr($protocolname, strlen($dirname)));
						if(class_exists($onlyprot))
							$this->$dirname->$protocol = new $onlyprot($this);
					}
					if(isset($this->$dirname->$protocol->__requirements)) {
						$this->$original = $this->$dirname->$protocol;
						$this->add($this->$dirname->$protocol->__requirements);
						return $this->keyword("more");
					}
					$this->$original = $this->$dirname->$protocol;
					return $this->keyword("ok");
				}
			}
		}else
			return $this->keyword("no_plugin_namespace");
	}
	function getVersion() {
		return $this->version;
	}
}
