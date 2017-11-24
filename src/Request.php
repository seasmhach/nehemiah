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
 * Initialize object properties with request parameters.
 *
 * @author Seasmhach <nehemiah@dovemail.eu>
 * @version 1.0.0 Initial version.
 */
trait Request {
	/**
	 * Get variables from request array. Check if there's a property in the
	 * current class. If so, set the property to the request parameter value.
	 *
	 * To illustrate what happens:
	 *
	 * $this->some_property = $_REQUEST['some_property'];
	 *
	 * 27-5-16: Added type casting. When for example $this->some_property is
	 * declared as integer, The value in the $_REQUEST global will be type
	 * casted and then assigned to the property.
	 *
	 * 18-12-16: Added support for $_FILES.
	 *
	 * 14-03-17: Added support for the HTTP Accept header
	 *
	 * @return void
	 */
	protected function request() {
		foreach ($_REQUEST as $property => &$raw_value) {
			if (property_exists($this, $property)) {
				settype($raw_value, gettype($this->$property));

				$this->$property = $raw_value;
			}
		}

		foreach ($_FILES as $property => $files) {
			if (property_exists($this, $property)) {
				if (is_array($files['error'])) {
					foreach ($files['error'] as $index => $error_code) {
						if ($error_code === UPLOAD_ERR_OK) {
							$this->$property[] = array(
								'name' => $files['name'][$index],
								'mime' => $files['type'][$index],
								'size' => $files['size'][$index],
								'location' => $files['tmp_name'][$index]
							);
						}
					}
				} else {
					$this->$property = array(
						'name' => $files['name'],
						'mime' => $files['type'],
						'size' => $files['size'],
						'location' => $files['tmp_name']
					);
				}
			}
		}

		if (property_exists($this, 'accept') && is_string($accept = filter_input(INPUT_SERVER, 'HTTP_ACCEPT'))) {
			list($types) = explode(';', $accept);

			$this->accept = array_map('trim', explode(',', $types));
		}
	}
}
