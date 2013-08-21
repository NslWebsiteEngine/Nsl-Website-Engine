<?php
class data extends base {
	function __get($time) {
		$time = substr($time, 1);
		$time = time() - $time;
		$divisors = array('year' => 31536000, 'month' => 2628000, 'day' => 86400, 'hour' => 3600, 'minute' => 60, 'second' => 1);
		$out = array();
		foreach($divisors as $name => $divisor) {
			if($value = floor($time / $divisor)) {
				if(!in_array($name, array("hour", "minute", "second")))
					$out[] = "{$value} {$name}".($value == 1 ? "" : "s");
				else
					$out[] = "{$value} {$name}".($value == 1 ? "" : "s");
			}
			$time %= $divisor;
		}
		if(empty($out))
			$out[] = "just now";
		return implode(", ", $out);	
	}
    function sum($btime, $time, $format = "yyyy-mm-dd") {
        $format = str_replace(array("/", "."), "-", $format);
        if(is_int($time))
            return $btime + $time;
        elseif(is_string($time)) {
            if($format == "yyyy-mm-dd")
                $time = strtotime($time);
            elseif($format == "dd-mm-yyyy") {
                $_time = explode("-", $time);
                $time = $_time[2]."-".$_time[1]."-".$_time[0];
                $time = strtotime($time);
            }
            return $btime + $time;
        }else
            $this->main->trigger_error("The time parameter can be either string or int");
    }
    function addSeconds($time, $count) {
        return $time + $count;
    }
    function addMinutes($time, $count) {
        return $time + (60*$count);
    }
    function addHours($time, $count) {
        return $time + (60*60*$count);
    }
    function addDays($time, $count) {
        return $time + (60*60*24*$count);
    }
    function addMonths($time, $count) {
        return $time + (60*60*24*30*$count);
    }
    function addYears($time, $count) {
        return $time + (60*60*24*365*$count);
    }
}
