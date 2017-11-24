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

/**
 * Simple session interface class.
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version.
 */
class Session {
	/**
	 * Set a session valiable. The variable becomes available in $_SESSION.
	 *
	 * @param string $key   Array key for variable
	 * @param mixed $value  Value
	 */
	public static function set(string $key, $value) {
		self::start();

		$_SESSION[$key] = $value;
	}

	/**
	 * Get value from session. Return default value if the value is found.
	 *
	 * @param  string $key           Array key for variable
	 * @param  mixed $default_value  The value to return if the key is not found
	 * @return mixed                 The value or default value if not found
	 */
	public static function get(string $key, $default_value) {
		self::start();

		return $_SESSION[$key] ?? $default_value;
	}

	/**
	 * Gets the value and the removes the value from the session. Return default
	 * value if the value is found.
	 *
	 * @param  string $key           Array key for variable
	 * @param  mixed $default_value  The value to return if the key is not found
	 * @return mixed                 The value or default value if not found
	 */
	public static function pull(string $key, $default_value) {
		$value = self::get($key, $default_value);

		self::unset($key);

		return $value;
	}

	/**
	 * Check if session has variable.
	 *
	 * @param  string  $key Array key for variable
	 * @return bool
	 */
	public static function has(string $key) {
		self::start();

		return isset($_SESSION[$key]);
	}

	/**
	 * Unset session vairable.
	 *
	 * @param  string $key Array key for variable
	 * @return void
	 */
	public static function unset($key) {
		if (session_status() === PHP_SESSION_ACTIVE) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Start session.
	 *
	 * @return void
	 */
	private static function start() {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
	}
}
