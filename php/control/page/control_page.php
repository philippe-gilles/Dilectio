<?php

// Classe abstraite page
abstract class control_page {
	protected $frame = null;
	protected $layout = null;

    abstract protected function configure();
	
	public function render() {
		$this->configure();

		o_b::clean();
		$this->frame->open();
		$this->layout->render();
		$this->frame->close();
		o_b::flush();
	}
}