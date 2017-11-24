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
 * Simple trait based observer pattern implementation.
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version.
 */
trait Mediator {
	/** @var array List of listeners */
	private static $listeners = [];

	/**
	 * Create hook. The first parameter to this method should be the hook identifier.
	 * All other (optional) parameters will be passed on as parameters to the listener.
	 *
	 * @return void
	 */
	protected static function hook() {
		$arguments = func_get_args();
		$hook = array_shift($arguments);

		if (isset(self::$listeners[$hook])) {
			foreach (self::$listeners[$hook] as $callback) {
				call_user_func_array($callback, $arguments);
			}
		}
	}

	/**
	 * Similar to Mediator::hook. The first parameter to this method should be the hook identifier.
	 * The second parameters should be the value you'd like to apply filters on.
	 * All other (optional) parameters will be passed on as parameters to the listener.
	 *
	 * @return mixed The second parameter after the filters are applied
	 */
	protected static function filter() {
		$arguments = func_get_args();
		$filter = array_shift($arguments);

		if (isset(self::$listeners[$filter])) {
			foreach (self::$listeners[$filter] as $callback) {
				$arguments[0] = call_user_func_array($callback, $arguments) ?? $arguments[0];
			}
		}

		return $arguments[0];
	}

	/**
	 * Add listeners. You can either add a single name as identier (string) or
	 * set and array of identifiers if you want to set multiple identifiers for
	 * your listener.
	 *
	 * @param  string|array $hook   Identifier(s)
	 * @param  object $callback 	Callback function/method
	 * @return void
	 */
	public static  function listen($hook, $callback) {
		if (is_string($hook)) {
			self::$listeners[$hook][] = $callback;
		} elseif (is_array($hook)) {
			foreach ($hook as $identifier) {
				self::$listeners[$identifier][] = $callback;
			}
		}
	}
}
