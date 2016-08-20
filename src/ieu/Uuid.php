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

/**
 * Provides a UUID type. Currently only version 4 is implemented,
 * but the class is written to handle all protocoll versions in
 * the future.
 *
 * @todo Implement the missing protocoll versions (1,2,3 and 5)
 *  
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 */

class Uuid {

	const PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-([1-5])[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

	/**
	 * The UUID
	 * @var string
	 */
	
	private $id;


	/**
	 * The version of this instance
	 * @var integer
	 */
	
	private $version;

	public function __construct($id)
	{
		$id = (string) $id;

		if (0 === preg_match(self::PATTERN, $id, $matches)) {
			throw new \Exception(sprintf('The given ID \'%s\' is not a valid UUID', $id));
		}

		$this->id = $id;
		$this->version = $matches[1];
	}

	public function equals(Uuid $id)
	{
		return $this->id === (string) $id;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function __toString()
	{
		return (string)$this->id;
	}


	/**
	 * Generates a UUID of the given version.
	 *
	 * @throws \InvalidArgumentException 
	 *    If the given version is unknown/not supported
	 *    
	 * @param  integer $version
	 *    The version (1 - 5)
	 *
	 * @return string
	 *    The UUID
	 */
	
	public static function get($version = 4)
	{
		switch($version) {
			case 4:
				return self::v4();
		}

		throw new \InvalidArgumentException(sprintf('Unknown/unsupported UUID version %s', $version));
	}


	/**
	 * Validates a UUID.
	 *
	 * @param  string  $id
	 *    The UUID to test
	 *
	 * @return boolean
	 *    Wether or not the UUID is valid
	 */
	
	public static function valid($id)
	{
		return 0 !== preg_match(self::PATTERN, (string)$id);
	}


	/**
	 * Generates a UUID of version 4.
     *
     * @param boolean $object
     *    Wether to return an Uuid instance or a string
     * 
	 * @return string
	 *    The UUID
	 */
	
	public static function v4($object = false) 
	{
		$id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,
			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);

		return $object ? new self($id) : $id;
	}
}