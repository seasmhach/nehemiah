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
use DomainException;
use PDO;

/**
 * PDO intergration class.
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version.
 */
class DataObject {
	/** @var array See DataObject::register() */
	private static $databases = [];

	/**
	 * Register database connection details. You can register multiple connection
	 * settings. These connection settings are tightly related to the PDO
	 * constructor.
	 *
	 * @see http://php.net/manual/en/pdo.construct.php
	 *
	 * @param  string $driver        Database driver
	 * @param  string $database_name Database name
	 * @param  string $host          Host including port if the server doesn't listen on the default port.
	 * @param  string $username      Database username
	 * @param  string $password      Database password
	 * @param  array  $pdo_options   PDO options (array passed as fourth parameter to the PDO constructor)
	 * @return void
	 */
	public static function register(string $driver, string $database_name, string $host, string $username, string $password, array $pdo_options = []) {
		self::$databases[$database_name] = [
			'dsn' => $driver . ':host=' . $host . ';dbname=' . $database_name,
			'username' => $username,
			'password' => $password,
			'pdo_options' => $pdo_options,
			'pdo_instance' => null,
		];
	}

	/**
	 * Get a PDO instance for the specified database name. PDO connections are
	 * presistent troughout a session. That means that a connection will only
	 * be one type established per database.
	 *
	 * @param  string $database_name Database name
	 * @return PDO                   PDO instance
	 */
	public static function pdo_instance(string $database_name) {
		if (!isset(self::$databases[$database_name])) {
			throw new DomainException("Connection settings for database '$database_name' are not registered.");
		}

		if (!self::$databases[$database_name]['pdo_instance'] instanceof PDO) {
			$databases[$database_name]['pdo_instance'] = new PDO($databases[$database_name]['dsn'], $databases[$database_name]['username'], $databases[$database_name]['password'], $databases[$database_name]['pdo_options']);
		}

		return self::$databases[$database_name]['pdo_instance'];
	}
}
