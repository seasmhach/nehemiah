<?php

/*
 * The MIT License
 *
 * Copyright 2017 Seasmhach <nehemiah@dovemail.eu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Seasmhach\Nehemiah;
use Seasmhach\Nehemiah\Session;

/**
 * Simple session handler.
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version
 */
class Session {
	/** @var Session */
	private static $instance;

	/**
	 * Set session variable.
	 *
	 * @param string $name Name of session property
	 * @param mixed $value
	 */
	public function __set(string $name, $value) {
		$_SESSION[$name] = $value;
	}

	/**
	 * Get session variable
	 *
	 * @param string $name Name of session property
	 * @return mixed Value of session property
	 */
	public function __get(string $name) {
		return $_SESSION[$name] ?? null;
	}

	/**
	 * Unset session variable
	 *
	 * @param string $name Unset session property
	 */
	public function __unset(string $name) {
		unset($_SESSION[$name]);
	}

	/**
	 * Destroy session
	 *
	 * @return int Session status ID
	 */
	public function destroy() {
		if (session_status() === PHP_SESSION_ACTIVE) {
			session_destroy();

			unset($_SESSION);
		}

		return session_status();
	}

	/**
	 * Singleton pattern. Get install of Nehemiah\Session
	 *
	 * @return Session
	 */
	public static function getInstance() {
		if (null === static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor starts session if not already started
	 */
	protected function __construct() {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
	}

	/** @return void */
	private function __clone() {}
	/** @return void */
	private function __wakeup() {}
}
