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
use AltoRouter;
use OutOfBoundsException;
use Exception;
use Route\DomainBasedRoutes;

/**
 * Bootstrapping the framework
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version
 */
class Bootstrap {


	/**
	 * Set the projects root and url.
	 *
	 * @param string $project_root Absolute path to project root
	 */
	public function __construct(string $project_root) {
		define('NEHEMIAH_PATH', $project_root);
		define('NEHEMIAH_URL', (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST']);
	}

	/**
	 * Launch bootstrapper.
	 *
	 * @return void
	 */
	public function launch() {
		try {
			print_r(DomainBasedRoutes::routes); die;

			$router = new AltoRouter();
			$router->addRoutes(DomainBasedRoutes::routes[$_SERVER['HTTP_HOST']]);

			if (!$router->match()) {
				header("HTTP/1.0 404 Not Found");

				throw new OutOfBoundsException("404 Page not found");
			} else {

			}
		} catch (Exception $exception) {
			require_once NEHEMIAH_PATH . '/vendor/seasmhach/nehemiah/exception/exception.php';
		}
	}
}
