<?php
class Attribute {
	public $id;
	public $name;
	public $position;
	public $visible = true;
	public $variation = true;
	public $options;
	public $option;

	public static function withNameAndOption($name, $option) {
		$instance = new self();
		$instance->name = $name;
		$instance->option = $option;
		return $instance;
	}

	public static function withNameAndPosAndOptions($name, $position, $options) {
		$instance = new self();
		$instance->name = $name;
		$instance->position = $position;
		$instance->options = $options;
		return $instance;
	}

	public function getAsArray() {
		return array (
			'name' => $this->name,
			'option' => $this->option
		);
	}

}
?>