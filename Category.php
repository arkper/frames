<?php
class Category {
	public $id;
	public $name;
	public $slug;

	function __construct($id) {
		$this->id = $id;
	}
}
?>