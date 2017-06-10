<?php
class DefaultAttribute {
	public $id;
	public $name;
	public $option;

	function __construct($name, $option) {
		$this->name = $name;
		$this->option = $option;
	}
}
?>