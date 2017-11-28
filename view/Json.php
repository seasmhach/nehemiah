<?php

/*
 * The MIT License
 *
 * Copyright 2017 Tribal Trading <info@tribaltrading.eu>.
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

namespace Seasmhach\Nehemiah\View;

class Json {
	private $data = [];

	public static function factory(array $variables = []) {
		return new self($variables);
	}

	public function __construct(array $data = []) {
		$this->data = $data;
	}

	public function __set(string $name, $value) {
		$this->data[$name] = $value;
	}

	public function __get(string $name) {
		return $this->data[$name] ?? null;
	}

	public function stream(bool $success) {
		header('Content-Type: application/json');
		header("Expires: on, 01 Jan 1970 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		$response = json_encode([
			'success' => $success,
			'data' => $this->data
		]);

		die($response);
	}
}
