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
use Seasmhach\Nehemiah\Mediator;

/**
 * This trait provides the easiest way satisfying Nehemiah\AccessInterface.
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version.
 */
trait Access {
	use Mediator;

	/** @var Session */
	private $session = null;

	/**
	 * An associative array that tells which type of users have access to which
	 * methods. A user of type 'admin' (system reserved) has access to everything
	 * by default.
	 *
	 * This property is usually initialised by the constructor of the controller
	 * using this trait.
	 *
	 * Here's an illustration of all possible values:
	 *
	 * $permission['some_method'] = true; // Everybody, including unauthenticated users have access.
	 * $permission['some_method'] = 'customer'; // Users of type 'customer' and 'admin' have access.
	 * $permission['some_method'] = ['customer', 'visitor']; // Users of type 'customer', 'visitor' and 'admin' have access.
	 *
	 * @var array Associative array tracking the access to methods
	 */
	protected $permission = [];

	/**
	 * Inquire about access to a method.
	 *
	 * @param  string $method Name of the method
	 * @return bool           Allow or deny access
	 */
	public function access_to(string $method) :bool {
		$permission = $this->permission[$method] ?? false;
		$type = null;

		if (true === $this->logged_in()) {
			$type = $this->session->user_type;

			Mediator::hook('session_verified');
		}

		if ($type === 'admin' || true === $permission) {
			return true;
		} elseif (is_array($permission) && in_array($type, $permission)) {
			return true;
		} elseif ($permission === $type) {
			return true;
		}

		return false;
	}

	/**
	 * Checks the session to see if a user is logged in.
	 *
	 * @todo: This needs to be moved to a user based APP end be checked from there. A interface might need to be created.
	 *
	 * @return bool Logged in or not
	 */
	protected function logged_in() {
		$this->session = Session::getInstance();

		/**
		 * Checking current session on three points.
		 *
		 * 1) The 'uid' variable is set to an integer in the session.
		 * 2) The 'logged_in' variable is set to true.
		 * 3) The IP-address of the time when the session was created, still matches the current IP.
		 */
		return (is_int($this->session->uid) &&
				true === $this->session->logged_in &&
				$this->session->ip === filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP));
	}

	/**
	 * Extends a user session by setting it's 'last_active' time to now.
	 * @todo Bring this functionality back using Mediator.
	 */
	//protected function extend_session() {
	//	User::update_by_user_id($this->session->uid, ['last_active' => 'now']);
	//}
}
