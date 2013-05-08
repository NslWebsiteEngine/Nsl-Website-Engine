<?php
class data extends base {
    function __get($time) {
        $time = substr($time, 1);
        $time = time() - $time;
        $divisors = ['year' => 31536000, 'month' => 2628000, 'day' => 86400, 'hour' => 3600, 'minute' => 60, 'second' => 1];
        $out = [];
        foreach($divisors as $name => $divisor) {
            if($value = floor($time / $divisor)) {
                if(!in_array($name, ["hour", "minute", "second"]))
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
}