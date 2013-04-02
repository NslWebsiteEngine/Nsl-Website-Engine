<?php
class data {
	function nicetime($fromDate, $toDate = NULL, $precision = -1, $separator = ', ', $divisors = NULL) {
		$dates = [$fromDate, $toDate];
		$difference = time() - $fromDate;
		return $this->format_interval($difference, $precision, $separator, $divisors);
	}
	function format_interval($seconds, $precision = -1, $separator = ', ', $divisors = NULL) {
		if(!isset($divisors)) {
			$divisors = [
				'Year' => [
					"time" => 31536000,
					"multiple" => "Years"
				], 
				'Month' => [
                    "time" => 2628000,
                    "multiple" => "Months"
                ], 
                "Day" => [
                    "time" => 86400,
                    "multiple" => "Days"
                ],
                "Hour" => [
                    "time" => 3600,
                    "multiple" => "Hours"
                ],
                "Minute" => [
                    "time" => 60,
                    "multiple" => "Minutes"
                ],
                "Second" => [
                    "time" => 1,
                    "multiple" => "Seconds"
                ]
			];
		}
		arsort($divisors);
		foreach($divisors as $name => $divisor) {
			if($value = floor($seconds / $divisor["time"])) {
                if(!in_array($name, ["Ora", "Minuto", "Secondo"]))
                    return "{$value} ".($value == 1 ? $name : $divisor["multiple"]);
				if($value == 1)
					$out[] = "$value $name";
				else
					$out[] = "$value ".$divisor["multiple"];
				if(--$precision == 0)
					break;
			}
			$seconds %= $divisor["time"];
		}
		if(!isset($out))
			$out[] = "0 ".$name;
		return implode($separator, $out);

	}
}