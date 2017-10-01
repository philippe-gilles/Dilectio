<?php

// Classe abstraite frame
abstract class view_frame {
	protected $tiers = array();
	protected $types = array();

	public function set_tiers() {
		foreach (func_get_args() as $arg) {
			$this->tiers[] = $arg;
		}
	}
	public function set_type() {
		foreach (func_get_args() as $arg) {
			$this->types[] = $arg;
		}
	}

    abstract public function open();
    abstract public function close();
}