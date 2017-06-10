<?php
class Variant {
	public $sku;
	public $regular_price;
	public $image;
	public $attributes;

	function __construct($sku, $regular_price, $image, $attributes) {
		$this->sku = $sku;
		$this->regular_price = $regular_price;
		$this->image = $image;
		$this->attributes = $attributes;
	}


	function get_array() {
		return array (
			'sku' => $this->sku,
			'regular_price' => $this->regular_price,
			'image' => $this->image,
			'attributes' => $this->attributes
		);
	}

	public function getAsArray() {
		$all_attr = array();

		foreach ($this->attributes as $attribute) {
			array_push($all_attr, $attribute->getAsArray());
		}

		return array (
			'sku' => $this->sku,
			'regular_price' => $this->regular_price,
			'image' => ['id' => $this->image->id],
			'attributes' => $all_attr
		);
	}

}
?>