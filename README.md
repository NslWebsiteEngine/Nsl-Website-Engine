
# NSL Website Engine

This engine was originally developed for the new version of New Social Life, which is still in development :). This engine is really powerful, something that you have to try.

## Getting Started

A simple usage of the library would be something like this: 
:::php
	

	function test(string $a, number $b, mixed $c) {
		if(is_array($c))
			return ["a"];
		else
			return ++$number;
	}
	function test2(number $a, boolean $b) {
		if($a == 5)
			return $b;
		return false; 
	}
	test("Hello", 12, []); // returns an array with an element a
	test("Hello", 12.1, false); // return 13.1
	test("Hello", 12.0, false); // return 13.0
	test(12, "Hello", false); // THIS ONE THROWS A PHP ERROR
	test2(2, true); // returns false
	test2(5, true); // return true
	test2(5, false); // returns false
	test2("hello", false); // THIS ONE THROWS A PHP ERROR

	
Of course this is just one of the thousand features you can use with my library.


## License
Developer: Danny Morabito
You can use this in your projects but the developers' names HAVE to be in the comment of every file.
