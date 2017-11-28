<?php

namespace Seasmhach\Nehemiah\View;

use Config\{Paths, Twig as Config};
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Custom Twig wrapper that allows us to set default options and template globals
 */
class Twig extends Twig_Environment {
	/**
	 * Fast Forward. Conventient method to instantiate and render a Twig template
	 * in one swoop.
	 *
	 * @param  string $template  Name of the template
	 * @param  string $view      Filename of view file
	 * @param  array  $variables Variables to pass to template
	 * @return string            Rendered template
	 */
	public static function ff(string $template, string $view, array $variables = []) {
		$twig = new self($template);

		return $twig->render($view, $variables);
	}

	/**
	 * Instantiate Twig and set our options and globals.
	 *
	 * @param string $template  Name of the template
	 * @param array  $paths     Array of paths to pass to the loader
	 */
	public function __construct(string $template, array $paths = []) {
		$loader = new Twig_Loader_Filesystem(Paths::TEMPLATES . '/' . $template);

		foreach ($paths as $path) {
			$loader->addPath($path);
		}

		parent::__construct($loader, Config::OPTIONS);

		foreach (Config::GLOBALS as $name => $value) {
			$this->addGlobal($name, $value);
		}
	}
}
