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
	public $pluginspath; // the path where plugins are stored
	public $defaults; // the default configuration
	public $__usedplugin = array(); // the loaded plugins
	public $configuration = array(); // the current configuration
	private $version = "0.4.0b"; // the current version of the NSL-Website-Engine
	function __construct($configurationfile = null) {
		define("NSL_Website_Engine", $this->version); // the nsl website engine version should be set to a global constant
		define("DS", DIRECTORY_SEPARATOR); // DS is just the same as DIRECTORY_SEPARATOR but it's shorter
		$GLOBALS["NSLWebsiteEngine"] = &$this; // the NSlWebsiteEngine should be accessible globally
		$this->defaults = array(); // the default configuration
		// <configure the defaults> 
		$this->pluginspath = $GLOBALS["NSLWebsiteEngine/pluginspath"] = $this->defaults["pluginspath"] = __DIR__.DS."classes".DS;
		$this->configuration = $this->defaults = array(
			"plugins" => array(),
			"base" => array(
				"showerrors" => true,
				"expose" => true
			),
		);
		// </configure>
		// include the base plugin, standard for all the plugins
		include $this->pluginspath."base.php";
		// if the user gave a json configuration file
		if(!is_null($configurationfile)) {
			// configure the NSL-Website-Engine using it
			$this->configure($configurationfile);
			// add the plugins set in the configuration
			$this->add($this->configuration["plugins"]);
		}
		// if the user wants to expose that he's using the NSL-Website-Engine (default)
		if($this->configuration["base"]["expose"])
			// ... then send an header showing that the website is programmed with the NSL-Website-Engine
			header("X-Powered-By: NSL Website Engine/#".$this->version); 
	}
	function configure($file) {
		$newarray = json_decode(file_get_contents($file), true); // get the configuration from the given file and save it to newarray 
		return $this->configuration = array_merge($this->configuration, $newarray); // merge the newarray with the configuration 
	}
	function __get($name) {
		// if the plugin isn't loaded
		if(!isset($this->$name)) {
			// if there's a plugin with the givenname
			if(file_exists($this->pluginspath.DS.$name.".php"))
				$this->trigger_error("Class {$name} isn't loaded"); // report an error
		// if the plugin is loaded
		}else
			// then return the plugin
			return $this->$name;
	}
	function add($plugins) {
		// if the user passes an array like ["name1.plugin1", "plugin2", ...]
		if(is_array($plugins)) {
			// create an array of returns which contains all the plugins loaded
			$returns = array();
			// for each plugin
			foreach($plugins as $plugin)
				// add the plugin and set a value in return
				$returns[] = $this->_add($plugin);
			// return the array of returns
			return $returns;
		// if the user passes a string like "chosenname.pluginname"
		}elseif(is_string($plugins))
			// add this plugin and return it
			return $this->_add($plugins);
		// if the user passes something else just return false
		return false;
	}
	// set and using are just aliases to add()
	function set($plugin) { return $this->add($plugin); }
	function using($plugin) { return $this->add($plugin); }
	// plugin is an alias to __get
	function plugin($plugin) {
		return $this->$plugin;
	}
	function trigger_error($error = "", $editor = "") {
		// if the error showing is enabled
		if($this->configuration["base"]["showerrors"]) {
			// if the prettyerrors plugin is enabled
			if($this->is_included("prettyerrors", true)) {
				// set $prettyerrors to the name of the prettyerrors plugin
				$prettyerrors = $this->is_included("prettyerrors");
				// did I already send an error report?
				// if not then setup prettyerrors plugin
				if(!isset($this->__prettyobject)) {
					$ed = $this->__prettyobject = $this->$prettyerrors->setTitle()->setArgs("NSL args", array(
						"Used plugin" => $this->__usedplugin,
						"Removed plugin" => $this->__removed
					));
					// if the user choose to use an editor
					if($editor != "")
						// then set this editor in the error reporting
						$ed->setEditor($editor);
					$ed->register();
				}
				// send the error to user
				throw new RuntimeException($error);
			}else
				// send the error to user
				die("<div class='nslerror'><b>NSL ERROR</b>: <i>{$error}</i></div>");
		}
	}
	function is_included($plugin_name, $truefalse = false) {
		// return true and false or the choosen name
		return isset($this->__usedplugin[$plugin_name]) ? ($truefalse ? true : $this->__usedplugin[$plugin_name]) : false;
	}
	function _add($plugin) {
		// split plugin by .
		$plugin = explode(".", strtolower($plugin));
		// if plugin is an array containing two elements
		if(count($plugin) == 2) {
			// set name to the first element of the array ...
			$name = $plugin[0];
			// ... and plugin it'self to the second one
			$plugin = $plugin[1];
		}else
			// else set the name and the plugin to have the same name
			$plugin = $name = $plugin[0];
		// if the plugin $plugin isn't used
		if(!isset($this->__usedplugin[$plugin]))
			// set the name of the new plugin as $name in the usedplugin array
			$this->__usedplugin[$plugin] = $name; 
		// split plugin by -
		$plugin = explode("-", $plugin);
		// if no subplugin required
		if(count($plugin) == 1)
			// plugin and folderName are set to the element nr. 0
			$plugin = $folderName = $plugin[0];
		// if it's a subplugin
		elseif(count($plugin) == 2) {
			// set the folder name to the path 0 of the plugin
			$folderName = $plugin[0];
			// "set the plugin name to the plugin name" :P
			$plugin = $plugin[1];
		}else // if it's something else then
			// just report an error
			$this->trigger_error("This function isn't developed. If you want this function implemented, please write an issue in the <a href='https://github.com/NslWebsiteEngine/Nsl-Website-Engine/issues'>NSL-Website-Engine Issues</a> page");
		// when $name isn't already used
		if(!isset($this->$name)) {
			// if the plugin was added but removed
			if(isset($this->__removed[$plugin])) {
				// take back the removed plugin
				$this->$name = $this->__removed[$plugin];
				// remove it from the removed list
				unset($this->__removed[$plugin]);
				return "ok";
			}else{
				// check if the user required a foldered plugin or not
				if(is_dir($this->pluginspath.$folderName)) {
					// if the user specified the subplugin name
					if($plugin != $folderName)
						// set the filename to the plugin to require
						$filename = $folderName.DS.$plugin;
					// if the user didn't
					else {
						// check if the subfolder has a default plugin
						if(file_exists($this->pluginspath.$folderName.DS."default")) {
							// set the default plugin name
							$def_plug = file_get_contents($this->pluginspath.$folderName.DS."default");
							// then use that file to set the plugin name
							$filename = $folderName.DS.$def_plug;
							// set the plugin to the default
							$plugin = $def_plug;
						}
						// if there's no default plugin
						else
							$this->trigger_error("There's no default subplugin for this plugin and you didn't choose which one to use. What should I do?");
					}
				}else
					// if not include the right file
					$filename = $folderName;
				// set the filename variable to the absolute path of the to-add plugin
				$filename = $this->pluginspath.$filename.".php";
				// if the plugin exists
				if(file_exists($filename))
					// include it but check that it's not already included
					include_once($filename);
				else {
					// show an error to the user
					$this->trigger_error("Unable to find {$plugin} plugin. {$filename}");
					return "not ok";
				}
				// add the plugin to the variable
				$this->$name = new $plugin($this);
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
									$this->trigger_error("The plugin {$plugin} isn't compatible with your php version ( >= {$value} )");
									return "not ok";
								}
							}elseif($req == "version_max") {
								if(!version_compare(PHP_VERSION, $value, "<=")) {
									$this->trigger_error("The plugin {$plugin} isn't compatible with your php version ( <= {$value} )");
									return "not ok";
								}
							}else{
								// if it's not a version requirement check if the user has the required function and if not report an error
								if(!function_exists($value) || !is_callable($value)) {
									$this->trigger_error("The plugin {$plugin} requires the function ({$value}) which isn't enabled in your server");
									return "not ok";
								}
							}
						}
					}
					// add all the other requirements
					$this->add($this->$name->__requirements);
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
		// return the version of the NSL-Website-Engine
		return $this->version;
	}
}