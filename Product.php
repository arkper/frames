<?php
require __DIR__ . '/Category.php';
require __DIR__ . '/Tag.php';

class Product {
	public $id;
	public $sku;
	public $name;
	public $type;
	public $short_description;
	public $categories;
	public $images;
	public $attributes;
	public $default_attributes;
	public $price;
	public $regular_price;
	public $sale_price;
	public $manage_stock;
	public $in_stock;
	public $tags;
	public $brand_name;
	public $model;
	public $gender;
	public $lens_color;
	public $shape;
	public $temple_material;
	public $front_material;
	public $style;
	public $collection;
	public $temple_length;
	public $bridge_size;
	public $lens_width;
	public $lens_height;
	public $variants;

	function __construct() {
		$this->variants = array();
	}

	function description() {
		
		$size_table = "<table><tr><th></th><th><img src=\"/size_vertical.gif\"/></th><th><img src=\"/size_eye.gif\"/></th><th><img src=\"/size_bridge.gif\"/></th><th><img src=\"/size_temple.gif\"/></th></tr>";
		
		foreach($this->attributes as $attribute) {
			if ($attribute->name === 'Size') {
				$width = $this->lens_width;
				$height = $this->lens_height;
				$bridge = $this->bridge_size;
				$temple = $this->temple_length;
				
				$checked = "checked";
				$count = 0;
				foreach ($attribute->options as $option) {
					$size = $option;
					$size_table	.= "<tr><td><span><input type=\"radio\" id=\"size_$count\" name=\"size\" value=\"$size\" $checked>Size $size</span></td>" .
					//$size_table	.= "<tr><td><input type=\"text\" id=\"size_$count\" name=\"size\" value=\"$size\"/>Size $size</td>" .
					"<td align=\"center\">$width</td><td align=\"center\">$height</td><td align=\"center\">$bridge</td><td align=\"center\">$temple</td></tr>";
					
					$checked = "";
					$count++;
				}
			}
			
		}
		$size_table .= "</table>";
		
		return "<table><tr><td>Model: $this->brand_name $this->model</td><td>Gender: $this->gender</td></tr>" .
			"<tr><td>Lenses color: $this->lens_color</td><td>Shape: $this->shape</td></tr>" .
			"<tr><td>Temple material: $this->temple_material</td><td>Front material: $this->front_material</td></tr><tr>" .
			"<td>Style: $this->style</td><td>Collection: $this->collection</td></tr></table><br/>" .
			$size_table;
	}

	function short_description() {
		return $this->description();
	}

	function getName() {
		return $this->brand_name . " " . $this->model . " " . $this->gender . " " . $this->collection;
	}

	function get_variants() {
		$out = array();

		foreach ($this->variants as $variant) {
			array_push($out, $variant->get_array());
		}

		return $out;
	}

	function add_variant($new_variant){

		array_push($this->variants, $new_variant);

	}

	public static function fromArray($p, $categories, $tags) {

		$product = new self();

		$category = $categories[$p['collection']];

		$product->sku=$p['sku'];
		$product->type='variable';
		$product->categories=array(0 => new Category($category));
		$product->manage_stock=false;
		$product->in_stock=true;
		$product->tags=array(0 => new Tag($tags[$p['brand_name']]));
		$product->brand_name=$p['brand_name'];
		$product->model=$p['model'];
		$product->gender=$p['gender'];
		$product->lens_color=$p['lens_color'];
		$product->shape=$p['shape'];
		$product->temple_material=$p['temple_material'];
		$product->front_material=$p['front_material'];
		$product->style=$p['theme'];
		$product->collection=$p['collection'];
		$product->temple_length=$p['temple_length'];
		$product->bridge_size=$p['bridge_size'];
		$product->lens_width=$p['lens_witdth'];
		$product->lens_height=$p['lens_height'];

		return $product;
	}

	function getProductData() {

		$data = [
		    'name' => $this->getName(),
		    'type' => 'variable',
		    'status' => 'publish',
		    'featured' => true,
		    'description' => $this->description(),
		    'short_description' => $this->short_description(),
		    'virtual' => true,
		    'downloadable' => false,
			'categories' => $this->categories,
			'images' => $this->images,
			'attributes' => $this->attributes,
			'default_attributes' => $this->default_attributes,
			'tags' => $this->tags
		];
		
		print_r($data);

		return $data;
	}

	public function getVariantData() {

		$all_vars = array();

		foreach ($this->variants as $var) {
			array_push($all_vars, $var->getAsArray());
		}

		$data = [
			'create' => $all_vars
		];

		return $data;
	}

	function update($data) {
		$this->id = $data['id'];

		$image_data = array();

		foreach ($data['images'] as $image_element) {
			$url1 = $image_element['src'];

			foreach ($this->variants as $var) {
				$url2 = $var->image->src;
				$s1 = substr ($url1, strrpos($url1, '/'), strrpos($url1, 'x') - strrpos($url1, '/'));
				$s2 = substr ($url2, strrpos($url2, '/'), strrpos($url2, 'x') - strrpos($url2, '/'));
				//echo "src1=$s1::::src2=$s2\n";
				if ($s1 == $s2) {
					$img_id = $image_element['id'];
					//echo ("Updating image id to $img_id\n");
					$var->image->id = $img_id;
					$var->image->src = $image_element['src'];
				}
			}
		}

	}



}
?>