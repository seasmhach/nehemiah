<?php

/*
 * The MIT License
 *
 * Copyright 2017 nehemiah.
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
use Seasmhach\Nehemiah\ReverseEngineerDB;
use Seasmhach\Nehemiah\View\Twig;
use Config\Paths;

class Builder extends ReverseEngineerDB {
	/**
	 * Builder version number. The number representing the version of Builder.
	 * This property should be send to the template parser so the templates can
	 * display the version of Builder too.
	 *
	 * @var string
	 */
	private $version = '2.00';

	public function __construct(string $database) {
		parent::__construct($database);

		foreach ($this as &$table) {
			$native_foreign_keys = [];

			foreach ($table['foreign_keys'] as $foreign_key) {
				$native_foreign_keys[] = array_keys($foreign_key['columns']);
			}

			$table['native_foreign_keys'] = $native_foreign_keys;
		}

		$this->cross_reference_foreign_keys();

		foreach ($this as &$table) {
			$use = $references = $table['references'] = [];

			foreach ($table['foreign_keys'] as &$foreign_key) {
				$foreign_key['namespace'] = $use[] = str_replace(' ', '', ucwords(str_replace('_', ' ', $foreign_key['table'])));
				$references = array_keys($foreign_key['columns']);
			}

			$table['use'] = array_unique($use);

			if (count($references)) {
				$table['references'][] = array_map("unserialize", array_unique(array_map("serialize", $references)));
			}
		}
	}

	public function build(string $path) {
		if (!is_dir($path) || !is_writable($path)) {
			throw new DomainException("Builder can not write in directory '$path'.");
		}

		foreach ($this as $table_name => $table) {
			$namespace = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));
			$base_class = $path . '/Base' . $namespace . '.php';
			$user_class = $path . '/' . $namespace . '.php';

			$twig = new Twig('Builder', [
				Paths::TEMPLATES . '/Builder/' . ((float) $this->version + 0),
				Paths::TEMPLATES . '/Builder/' . ((float) $this->version + 0) . '/' . $this->version,
			]);

			$base = $twig->render('base.twig', [
				'database' => $this->database,
				'version' => $this->version,
				'table_name' => $table_name,
				'namespace' => $namespace,
				'table' => $table,
			]);

			file_put_contents($base_class, $base);

			/**
			 * Only generating user class if it doesn't already exist. The user
			 * class extends the base class and is for users to add their code.
			 */
			if (!is_file($user_class)) {
				$twig = new Twig('Builder', [
					Paths::TEMPLATES . '/Builder/' . ((float) $this->version + 0),
					Paths::TEMPLATES . '/Builder/' . ((float) $this->version + 0) . '/' . $this->version,
				]);

				$user = $twig->render('user.twig', [
					'database' => $this->database,
					'version' => $this->version,
					'table_name' => $table_name,
					'namespace' => $namespace,
					'table' => $table,
				]);

				file_put_contents($user_class, $user);
			}
		}
	}

	protected function cross_reference_foreign_keys() {
		foreach ($this as $table_name => $table) {
			foreach ($table['foreign_keys'] as $foreign_key) {
				/**
				 * Skip the ones that are already cross-referenced
				 */
				if (!isset($foreign_key['cross_referenced'])) {
					$this[$foreign_key['table']]['foreign_keys'][] = [
						'cross_referenced' => true,
						'table' => $table_name,
						'columns' => array_flip($foreign_key['columns']),
					];
				}
			}
		}
	}
}
