<?php
class Image {
	public $id;
	public $src;
	public $name;
	public $position;

	public static function withId($id) {
		$instance = new self();
		$instance->id = $id;
		return $instance;
	}

	public static function withSrcAndPosition($src, $position) {
		$instance = new self();
		$instance->src = $src;
		$instance->position = $position;
		return $instance;
	}
}
?>