<?php

namespace Seasmhach\Nehemiah;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Central class for handling user input errors.
 */
class UserInput {
	/**
	 * @var array $errors A list of error messages
	 */
	private $errors = [];

	/**
	 * @var array $fields A list of form controls that have incorrect values
	 */
	private $fields = [];

	/**
	 * Add an user input error. The first parameters is the actual error message
	 * all other parameters should be form field names. They are not documented
	 * because you can pass as many as you like. It's up to the GUI to highlight
	 * them.
	 *
	 * @param string $error_message Error message
	 * @return void
	 */
	public function add_error(string $error_message) {
		$this->errors[] = $error_message;
		$arguments = func_get_args();

		for ($i = 1; $i < count($arguments); $i++) {
			$this->fields[] = $arguments[$i];
		}
	}

	/**
	 * @return bool Tell if errors where registred
	 */
	public function has_errors() {
		return (bool) count($this->errors);
	}

	/**
	 * Send the input error information back to the UA in JSON format.
	 * @param  string $message Message
	 * @return JsonResponse Json Response with 422 HTTP status code
	 */
	public function json_response(string $message) {
		$response = new JsonResponse([
			'message' => $message,
			'errors' => $this->errors,
			'fields' => $this->fields,
		], 422);

		return $response;
	}
}
