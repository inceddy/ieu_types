<?php

/*
 * This file is part of ieUtilities Types.
 *
 * (c) 2016 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ieu;

/**
 * Provides an extension of the native String-type and 
 * static utilitiemethods for Strings and String-objects.
 * 
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 */

class String {

	const FORMAT_PATTERN = "/\{(\w+)\}/";
	const FORMAT_PATTERN_COMPLEX = "/\{(\w+)?%([+-]?(?:[ 0]|'.{1})?-?\d*(?:\.\d+)?[bcdeEufFgGosxX])\}/";

	private $string = '';

	private function __construct($string)
	{
		$this->string = $string;
	}

	public function __toString()
	{
		return $this->string;
	}

	public function concat(String $string, $separator = '')
	{
		return new self($this->string . $separator . $string);
	}

	public function equals(String $string, $options = 0)
	{
		return $this->string == (string)$string;
	}

	public function formatWith($arguments)
	{
		return self::from(self::format($this->string, $arguments));
	}



	/**
	 * Erweiterung von fprint mit benamten argumenten
	 *
	 * @param  string $string    der zu formatierende String
	 * @param  array  $arguments der Array mit allen Ersatzwerten (Name => Wert)
	 *
	 * @return string            der formatierte String
	 * 
	 */
	
	public static function format($string, $arguments = array())
	{
		return strpos($string, '%') === false ? self::formatSimple($string, $arguments) : self::formatComplex($string, $arguments);
	}

	private static function formatSimple($string, $arguments)
	{
		foreach($arguments as $key => $value) {
			$string = str_replace('{' . $key . '}', $value, $string);
		}

		return $string;
	}

	private static function formatComplex($string, $arguments)
	{
    	$values = [];

    	$count = 0;

    	while (preg_match(self::FORMAT_PATTERN_COMPLEX, $string, $match)) {
    		$key = $match[1] ?: $count;

    		if (!isset($arguments[$key])) {
    			$string = str_replace($match[0], '', $string);
    			continue;
    		}

    		$values[$count] = $arguments[$key];
	        $string = str_replace($match[0], '%' . ++$count . '$' . $match[2], $string);
    	}

    	return vsprintf($string, $values);
	}


	public static function from($string)
	{
		return new self($string);
	}
}