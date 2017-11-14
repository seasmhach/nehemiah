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
use Exception;
use DomainException;
use OutOfBoundsException;

/**
 * Bootstrapping the framework
 */
class Bootstrap {
	/**
	 * @var array Allowed landing domains (key) and their default route (value)
	 */
	private $landing_domains = [];

	/**
	 * Set the projects root and url.
	 *
	 * @param string $project_root Absolute path to project root
	 * @param string $project_url  Unique Resource Locator to this project
	 */
	public function __construct(string $project_root, string $project_url) {
		define('N_PATH', $project_root);
		define('N_URL', $project_url);
	}

	/**
	 * Specify a list of Landing domains and their default routes.
	 * Look into the AltoRouter documentation:
	 *
	 * @link http://altorouter.com/ AltoRouter documentation
	 * @example $landing_domains[
	 * 	'https://google.com' => '/search-engine',
	 * 	'my.website.com' => /frontpage
	 * ]
	 *
	 * @param array $landing_domains [description]
	 */
	public function set_landing_domains(array $landing_domains) {
		$this->landing_domains = $landing_domains;
	}

	/**
	 * Launch bootstrapper.
	 *
	 * @param  AltoRouter $router                     AltoRouter object
	 * @param  array $permission_denied_fallback Class namespace and method to call in case permission to the route is denied
	 * @return void
	 */
	public function launch(AltoRouter $router, array $permission_denied_fallback) {
		try {
			if (rtrim(N_URL . $_SERVER['REQUEST_URI'], '/') === N_URL) {
				$match = $router->match($this->landing_domains[N_URL]);
			} else {
				$match = $router->match();
			}

			if (!empty($match)) {
				list($namespace, $method) = $match['target'];

				if (!class_exists($namespace)) {
					throw new DomainException("Controller doesn't exist: " . $class);
				}

				$controller = new $namespace();

				if (!is_callable([$controller, $method])) {
					throw new DomainException("Method '$method' doesn't  exist in contoller: " . $class);
				} elseif (!is_callable([$controller, 'access_to'])) {
					throw new DomainException("Required method 'access_to' missing in class: " . $class);
				} elseif (true !== call_user_func([$controller, 'access_to'], $method)) {
					list($namespace, $method) = $permission_denied_fallback;

					$controller = new $namespace;

					call_user_func_array([$controller, $method], []);
				}
			} else {
				throw new OutOfBoundsException("The current request doesn't match any route");
			}
		} catch (Exception $exception) {
			echo $exception->getMessage();

			print_r($exception->getTrace());
		}
	}
}
