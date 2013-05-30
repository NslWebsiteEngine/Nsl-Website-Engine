<?php
class lib {
    protected $__keywords__;
	protected $__keywords_counter__ = 0xF0;
	protected $__removed = [];
	protected $pluginspath;
	protected $defaults;
	protected $__usedprotocols = [];
	function __construct() {
		$this->keyword("more");
		$this->keyword("ok");
		$this->keyword("already_there");
		$this->keyword("no_plugin_namespace");
		$this->defaults = new stdClass;
		$this->pluginspath = $GLOBALS["NSLWebsiteEngine/pluginspath"] = $this->defaults->pluginspath = __DIR__."/classes/";
		$this->setpluginspath($this->pluginspath);
		include $this->pluginspath."base.php";
	}
	function __get($name) {
		if(!isset($this->$name)) {
			if(file_exists($this->pluginspath."/".$name.".php"))
				$this->trigger_error("Class {$name} isn't loaded");
		}
	}
	function add($protocols) {
		if(is_array($protocols)) {
			$returns = [];
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
		if(substr($path, -1) != "/")
			$path .= "/";
		$this->pluginspath = $path;
		$GLOBALS["NSLWebsiteEngine/pluginspath"] = $this->pluginspath;
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
	function keyword($k) {
		if(!isset($this->__keywords__[$k])) {
			$this->__keywords__[$k] = $this->__keywords_counter__;
			$this->__keywords_counter__ += strlen($k);
		}
		return dechex($this->__keywords__[$k]);
	}
	function trigger_error($error = "", $editor = "") {
		if(isset($this->prettyerrors)) {
			if(!isset($this->__prettyobject)) {
		    	$ed = $this->__prettyobject = $this->prettyerrors->setTitle()->setArgs("NSL args", [
		    		"Used Protocols" => $this->__usedprotocols,
		    		"Removed Protocols" => $this->__removed
		    	]);
                if($editor != "")
                    $ed->setEditor($editor);
                $ed->register();
			}
			throw new RuntimeException($error);
		}else
			die("<div class='nslerror'><b>NSL ERROR</b>: <i>{$error}</i></div>");
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
				if(isset($this->$protocol->__requirements)) {
					$this->add($this->$protocol->__requirements);
					return $this->keyword("more");
				}
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
			if(file_exists($dir."/base.php")) // every sub level can have a base.php
				include_once $dir."/base.php";
			$protocol = strtolower(array_shift($protocol));
			if(!isset($this->$dirname->$protocol)) {
				$this->__usedprotocols[$dirname][] = $protocol;
				$this->__usedprotocols[] = $dirname.ucfirst(strtolower($protocol));
				if(isset($this->__removed[$dirname][$protocol])) {
					$this->$protocol = $this->__removed[$dirname][$protocol];
					unset($this->__removed[$dirname][$protocol]);
					return $this->keyword("ok");
				} else {
					$name = $dir."/".strtolower($protocol).".php";
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
}
