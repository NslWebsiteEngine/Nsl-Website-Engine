<?php
class jQuery {
	function __construct() {
		if(!file_exists("jquery.js"))
			file_put_contents("jquery.js", fopen("http://code.jquery.com/jquery-latest.min.js", "r"));
		else {
			$curl = curl_init('http://code.jquery.com/jquery-latest.min.js');
			curl_setopt($curl, CURLOPT_NOBODY, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FILETIME, true);
			$result = curl_exec($curl);
			$timestamp = curl_getinfo($curl, CURLINFO_FILETIME);
			if(filemtime("jquery.js") < $timestamp && $timestamp > 0)
				file_put_contents("jquery.js", fopen("http://code.jquery.com/jquery-latest.min.js", "r"));
			else
				return true;
		}
	}
}
