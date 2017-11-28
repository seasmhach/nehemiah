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
 * Parent controller for projects.
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version.
 */
abstract class Base {
	/**
	 * See ::access_to
	 *
	 * @var array
	 */
	protected $method_access = [];

	/**
	 * Access to controller actions is denied by default. The bootstrapper expects
	 * all controllers to have an 'access_to' method implementation. This is the
	 * default one.
	 *
	 * True is returned if one of the following conditions is met:
	 *
	 * 1) $access_level equals 'admin' or $self::method_access[$method] equals 'true'.
	 * 2) $access_level equals $self::method_access[$method].
	 * 3) $self::method_access[$method] is initialized as an array and $access_level
	 *    equals one of its values.
	 *
	 * This, of course is not a way to prevent public methods from being callable.
	 * It provides guidelines for your public class methods. Guidelines that the
	 * bootstrapper will honour.
	 *
	 * Keep in mind: The controller's constructor is ALWAYS called!!
	 *
	 * @param  string $method       Controller action to get access to.
	 * @param  string $access_level The current sessions access level
	 * @return bool                 Tells if access should be granted or not.
	 */
	public function access_to(string $method, string $access_level) {
		$permission = $this->method_access[$method] ?? false;

		if (true === $permission || $access_level === 'admin') {
			return true;
		} elseif (is_array($permission) && in_array($access_level, $permission)) {
			return true;
		} elseif (is_string($permission) && $access_level === $permission) {
			return true;
		}

		return false;
	}
}
