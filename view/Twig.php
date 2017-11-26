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

namespace View;
use Seasmhach\Nehemiah\Mediator;
use Config\{Paths, Twig AS Twig_Config};
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_StringLoader;

class Twig {
	use Mediator;

	protected $template = '';
	protected $paths = [];
	protected $variables = [];
	protected $options = [];

	public static function factory(string $template, array $variables = [], array $paths = []) {
		return new self($template, $variables, $paths);
	}

	public function __construct(string $template, array $variables = [], array $paths = []) {
		$this->template = $template;
		$this->variables = $variables;
		$this->paths = $paths;
		$this->options = Twig_Config::OPTIONS;
		/**
		 * The paths added to Twig Loader Filesystem have a hierarchy. The first
		 * the path where the view file will be found first, will be used. That's
		 * why we splice the template path to the first position.
		 */
		array_splice($this->paths, 0, 0, Paths::TEMPLATES . '/' . $template);
	}

	/**
	 * Sets a variable that's later on passed to Twig to make available in the
	 * template.
	 *
	 * @param string $name Name of variable
	 * @param mixed $value Value of variable
	 */
	public function __set(string $name, $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Gets one of the template variables.
	 *
	 * @param string $name Name of variable to get
	 * @return mixed Variable value
	 */
	public function __get(string $name) {
		return $this->variables[$name] ?? null;
	}

	/**
	 * Add a search path to Twig Loader Filesystem. This makes the files available
	 * to use in the template or to the extension mechanism of Twig.
	 *
	 * @param string $path Absolute path to directory container view file(s)
	 */
	public function add_path(string $path) {
		$this->paths = $path;
	}

	public function render(string $view, bool $enable_string_loader = false) {
		$this->paths[] = dirname($view);

		if (is_string($this->options['cache'])) {
			$this->options['cache'] = sprintf($this->options['cache'], $this->template);
		}

		$loader = new Twig_Loader_Filesystem($this->paths);
		$twig = new Twig_Environment($loader, $this->options);

		foreach (Twig_Config::GLOBALS as $name => $value) {
			$twig->addGlobal($name, $value);
		}

		if (true === $enable_string_loader) {
			$twig->addExtension(new Twig_Extension_StringLoader);
		}

		$template = $twig->loadTemplate(basename($view));

		return $template->render(self::filter('set_variables', $this->variables, $this->template));
	}
}
