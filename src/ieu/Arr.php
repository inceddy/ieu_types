<?php

/*
 * This file is part of ieUtilities.
 *
 * (c) 2016 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ieu;
use \ArrayAccess;
use \IteratorAggregate;
use \ArrayIterator;


/**
 * Provides an extension of the native Array-type and 
 * static utilitiemethods for Arrays and Arr-objects.
 * 
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 */

final class Arr implements ArrayAccess, IteratorAggregate {

	// Option constants
	const SPLIT_STRING = 0b00001;

	/**
	 * Native array this object is based on
	 * @var array
	 */
	
	private $arr = [];

	
	protected function __construct(array $arr)
	{
		$this->arr = $arr;
	}

	public function allowedKeys(array $allowed)
	{
		return new self(self::allowedKeysOnArray($allowed, $this->arr));
	}

	public function getIterator()
	{
		return new ArrayIterator($this->arr);
	}


    /**
     * Assigns a value to the specified offset
     *
     * @param string The offset to assign the value to
     * @param mixed  The value to set
     *
     * @return void
     * 
     */
   
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->arr[] = $value;
        } else {
            $this->arr[$offset] = $value;
        }
    }


    /**
     * Whether or not an offset exists
     *
     * @param string An offset to check for
     * 
     * @return boolean
     * 
     */

    public function offsetExists($offset) 
    {
        return isset($this->arr[$offset]);
    }


    /**
     * Unsets an offset
     *
     * @param string The offset to unset
     *
     * @return void
     * 
     */

    public function offsetUnset($offset) 
    {
        if ($this->offsetExists($offset)) {
            unset($this->arr[$offset]);
        }
    }


    /**
     * Returns the value at specified offset
     *
     * @param string The offset to retrieve
     * 
     * @return mixed
     * 
     */
 
    public function offsetGet($offset) 
    {
        return $this->offsetExists($offset) ? $this->arr[$offset] : null;
    }

	static function allowedKeysOnArray(array $allowed, array $values)
	{
		return array_intersect_key($values, array_flip($allowed));
	}

	static function from($arrayLike, $options = 0)
	{
		if (is_array($arrayLike)) {
			return new self($arrayLike);
		}

		if (is_string($arrayLike) && $options & self::SPLIT_STRING) {
			return new self(explode(',', $arrayLike));
		}

		return new self([$arrayLike]);
	}
}