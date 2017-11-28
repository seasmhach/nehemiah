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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Seasmhach\Nehemiah\DataObject;
use AltoRouter;
use OutOfBoundsException;
use DomainException;
use Exception;
use Config\DomainBasedRoutes;
use Config\Paths;
use Config\DB;

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
	public function launch(string $route = null) {
		try {
			$session = new Session();
			$account = $session->get('nehemiah_user');

			if (!isset(DomainBasedRoutes::routes[$_SERVER['HTTP_HOST']])) {
				throw new OutOfBoundsException("There are no routes defined for domain: '" . $_SERVER['HTTP_HOST'] . "'.");
			}

			$router = new AltoRouter();
			$router->addRoutes(DomainBasedRoutes::routes[$_SERVER['HTTP_HOST']]);

			if (!$match = $router->match($route)) {
				header("HTTP/1.0 404 Not Found");

				throw new OutOfBoundsException("404 Page not found");
			} else {
				list($controller, $action) = $match['target'];

				if (!class_exists($controller) || !($instance = new $controller)) {
					throw new DomainException("Controller doesn't exist: " . $controller);
				} elseif (!$instance->access_to($action, $account['type'] ?? '')) {
					if (is_null($route)) {
						$this->launch(DomainBasedRoutes::routes[$_SERVER['HTTP_HOST'] . '_access_denied']);
					} else {
						header("HTTP/1.0 403 Forbidden");

						throw new OutOfBoundsException("403 Forbidden");
					}
				} elseif (!is_callable([$controller, $action])) {
					throw new DomainException("Action '$action' is not defined in controller: " . $controller);
				} else {
					$this->register_mysql_connection();
					$this->invoke_plugins();

					$response = call_user_func_array([$instance, $action], $match['params'] ?? []);

					if (!$response) {
						throw new OutOfBoundsException("Action '$action' in controller '$controller' is returning void.");
					} elseif ($response instanceof Response) {
						$response->send();
					} else {
						echo $response;
					}
				}
			}
		} catch (Exception $exception) {
			require_once NEHEMIAH_PATH . '/vendor/seasmhach/nehemiah/exception/exception.php';
		}
	}

	private function register_mysql_connection() {
		DataObject::register(DB::DRIVER, DB::DB, DB::HOST, DB::USER, DB::PASS, DB::PDO_OPTIONS);
	}

	/**
	 * Invoking plugins.
	 *
	 * @return void
	 */
	private function invoke_plugins() {
		/**
		 * Invoke project plugins
		 */
		foreach (glob(Paths::PROJECTS . '/*', GLOB_ONLYDIR) as $project) {
			$plugin_namespace = 'Projects\\' . basename($project) . '\\Plugin';

			if (class_exists($plugin_namespace)) {
				new $plugin_namespace;
			}
		}

		/**
		 * Invoke template pluginss
		 */
		foreach (glob(Paths::TEMPLATES . '/*', GLOB_ONLYDIR) as $template) {
			$plugin_namespace = 'Templates\\' . basename($template) . '\\Plugin';

			if (class_exists($plugin_namespace)) {
				new $plugin_namespace;
			}
		}
	}
}
