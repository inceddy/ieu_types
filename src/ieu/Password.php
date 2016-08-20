<?php

/*
 * This file is part of ieUtilities.
 *
 * (c) 2015 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ieu;

/**
 * Provides an extension of the native String-type and 
 * static utilitiemethods for Strings and _String-objects.
 * 
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 */

class Password {

	private $salt = null;
	private $method = '';
	private $hash = '';

	private function __construct($hash, $method, $salt)
	{
		$this->hash = $hash;
		$this->method = $method;
		$this->salt = $salt;
	}

	public function __toString()
	{
		return $this->hash;
	}

	public function getSalt()
	{
		return $this->salt;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public static function from($password, $method = 'ssha512', $saltHex = null)
	{
		// Hashed password -> Extract method and salt
		if (preg_match('/\{([A-Z0-9]+)\}/i', $password, $match)) {

			$method = strtolower($match[1]);

			switch ($method) {
				case 'ssha256':
					$saltHex = substr(bin2hex(base64_decode(substr($password, 9))), 64);
					break;

				case 'ssha512':
					$saltHex = substr(bin2hex(base64_decode(substr($password, 9))), 128);
					break;
				default:
					$salt = null;
			}

			return new self($password, $method, $saltHex);
		}

		// Generalte new salt
		if ($saltHex === null) {
			$saltHex = bin2hex(openssl_random_pseudo_bytes(8)); 
		}

		$saltBin = hex2bin($saltHex);

		// Hash given password by giben method
		switch ($method) {
			case 'md5':
				return new self("{md5}" . base64_encode(hex2bin(md5($password))), $method, $saltHex);
			case 'sha256':
				return new self("{sha256}" . base64_encode(hex2bin(hash('sha256', $password))), $method, $saltHex);
			case 'sha512':
				return new self("{sha512}" . base64_encode(hex2bin(hash('sha512', $password))), $method, $saltHex);
			case 'ssha256':
				return new self("{ssha256}" . base64_encode(hex2bin(hash('sha256', $password . $saltBin)) . $saltBin), $method, $saltHex);
			case 'ssha512':
				return new self("{ssha512}" . base64_encode(hex2bin(hash('sha512', $password . $saltBin)) . $saltBin), $method, $saltHex);
		}

		throw new \Exception('Not known hashing method giben!');
	}

	public function equals($password)
	{
		return $this->hash == (string)self::from($password, $this->method, $this->salt);
	}
}

