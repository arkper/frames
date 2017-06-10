<?php
class Tag {
	public $id;
	public $name;
	public $slug;

	function __construct($id) {
		$this->id = $id;
	}
}
?>