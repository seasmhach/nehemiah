<?php

namespace View;
use View\Twig;
use Dompdf\Dompdf;

class PDF extends Twig {
	private $paper = 'A4';
	private $orientation = 'portrait';
	
	protected $filename = 'Document';
	
	public function __construct(string $template, array $variables = array(), array $paths = array()) {
		parent::__construct($template, $variables, $paths);
	}
	
	public function set_paper(string $paper) {
		$this->paper = $paper;
	}
	
	public function set_orientation(string $oriantation) {
		$this->orientation = $oriantation;
	}
	
	public function set_filename(string $filename) {
		$this->filename = $filename;
	}
	
	public function render(string $view, bool $enable_string_loader = false) {		
		$dompdf = new Dompdf([
			'enable_font_subsetting' => true,
			'isRemoteEnabled' => true,
			'isPhpEnabled' => true,
		]);

		$dompdf->loadHtml(parent::render($view, $enable_string_loader));
		$dompdf->setPaper($this->paper, $this->orientation);
		$dompdf->render();
		$dompdf->stream($this->filename . '.pdf');
	}
	
	public function output(string $view, bool $enable_string_loader = false) {
		$dompdf = new Dompdf([
			'enable_font_subsetting' => true,
			'isRemoteEnabled' => true,
			'isPhpEnabled' => true,
		]);

		$dompdf->loadHtml(parent::render($view, $enable_string_loader));
		$dompdf->setPaper($this->paper, $this->orientation);
		$dompdf->render();
		
		return [$this->filename . '.pdf', $dompdf->output()];
	}
}