<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Product.php';
require __DIR__ . '/Category.php';
require __DIR__ . '/Image.php';
require __DIR__ . '/Attribute.php';
require __DIR__ . '/DefaultAttribute.php';
require __DIR__ . '/Tag.php';
require __DIR__ . '/Variant.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'http://primeframes.com',
    'ck_36a52bde4292de18b8150360f46cd75c72571f80',
    'cs_c5143fca4217271cc67decb21b3bd5bc5a1febfc',
    [
        'wp_api' => true,
        'version' => 'wc/v2',
        'verify_ssl' => false,
        'query_string_auth' => true,
        'timeout' => 60
    ]
);

$product = new Product();
$product->sku = "8053672687583";
$product->name = "Polo Prep 0PP8527 47 BLACK TORTOISE Woman";
$product->type = "variable";
$product->description = "<table><tr><td>Model: Polo Prep 0PP8527</td><td>Size: 47</td></tr><tr><td>Gender: Woman</td><td>Color: BLACK TORTOISE</td></tr><tr><td>Lenses color: </td><td>Shape: Rectangle</td></tr><tr><td>Temple material: Plastic</td><td>Front material: Plastic</td></tr><tr><td>Stype: </td><td>Collection: Optical</td></tr><tr><td>Temple length: 130</td><td>Bridge size: 15</td></tr><tr><td>Lens width: 47.0</td><td>Lens height: 31.1</td></tr></table>";
$product->short_description = $product->description;
$product->categories = array(0 => new Category(25));
$product->images = array(
		0 => Image::withSrcAndPosition('https://my.luxottica.com/myLuxotticaImages/PP/0PP8527__1635_890x445.jpg', 0),
		1 => Image::withSrcAndPosition('https://my.luxottica.com/myLuxotticaImages/PP/0PP8527__1636_890x445.jpg', 1),
		2 => Image::withSrcAndPosition('https://my.luxottica.com/myLuxotticaImages/PP/0PP8527__1503_890x445.jpg', 2)
	);
$product->attributes = array(
		0 => Attribute::withNameAndPosAndOptions("Color", 0, array("BLACK TORTOISE", "NAVY BRIGHT BLUE", "BLACK RED")),
		1 => Attribute::withNameAndPosAndOptions("Size", 1, array(47, 49))
	);
$product->default_attributes = array(
		0 => new DefaultAttribute("Color", "BLACK TORTOISE"),
		1 => new DefaultAttribute("Size", 47)
	);

$product->price = 81.58;
$product->regular_price = 81.58;
$product->sale_price = 73.42;
$product->manage_stock = FALSE;
$product->in_stock = TRUE;
$product->tags = array (0 => new Tag(36));

//print_r ($product);

//print_r($woocommerce->delete('products/18364', ['force' => true]));


/*
$data = [
	    'regular_price' => "81.58"
];

print_r($woocommerce->put('products/18317', $data));

*/



try {

    $data = [
	    'name' => $product->name,
	    'type' => 'variable',
	    'status' => 'publish',
	    'featured' => true,
	    'description' => $product->description(),
	    'short_description' => $product->short_description(),
	    'virtual' => true,
	    'downloadable' => false,
		'categories' => $product->categories,
		'images' => $product->images,
		'attributes' => $product->attributes,
		'default_attributes' => $product->default_attributes,
		'tags' => $product->tags
	];

	$product_data = $woocommerce->post('products', $data);

	print_r($product_data);

	$new_var = new Variant(
			"8053672687583-1", "81.58", Image::withId("18388"), array(Attribute::withNameAndOption('Size', 47), Attribute::withNameAndOption('Color', 'BLACK TORTOISE'))
		);

	print_r ($new_var);

	$product->add_variant($new_var);

	$product->add_variant(new Variant("8053672687583-2", "81.58", new Image("18389"), array(new Attribute('Size', 49), new Attribute('Color', 'NAVY BRIGHT BLUE'))));

	$product->add_variant(new Variant("8053672687583-3", "81.58", new Image("18390"), array(new Attribute('Size', 49), new Attribute('Color', 'BLACK RED'))));

	$product->add_variant(new Variant("8053672687583-4", "81.58", new Image("18388"), array(new Attribute('Size', 49), new Attribute('Color', 'BLACK TORTOISE'))));

	$product->add_variant(new Variant("8053672687583-4", "81.58", new Image("18389"), array(new Attribute('Size', 47), new Attribute('Color', 'NAVY BRIGHT BLUE'))));



/*
	$data =
	[
		'create' => [
			[
	    		'sku' => '8053672687583-1',
	    		'regular_price' => '81.58',
	    		'image' => ['id' => $product['images'][1]['id']],
	    		'attributes' => [
	    			['name' => 'Size', 'option' => '47'],
	    			['name' => 'Color', 'option' => 'BLACK TORTOISE']
	    		]
	    	],
			[
	    		'sku' => '8053672687583-2',
	    		'regular_price' => '81.58',
	    		'image' => ['id' => $product['images'][2]['id']],
	    		'attributes' => [
	    			['name' => 'Size', 'option' => '49'],
	    			['name' => 'Color', 'option' => 'NAVY BRIGHT BLUE']
	    		]
			],
			[
	    		'sku' => '8053672687583-3',
	    		'regular_price' => '81.58',
	    		'image' => ['id' => $product['images'][3]['id']],
	    		'attributes' => [
	    			['name' => 'Size', 'option' => '49'],
	    			['name' => 'Color', 'option' => 'BLACK RED']
	    		]
			],
			[
	    		'sku' => '8053672687583-4',
	    		'regular_price' => '81.58',
	    		'image' => ['id' => $product['images'][1]['id']],
	    		'attributes' => [
	    			['name' => 'Size', 'option' => '49'],
	    			['name' => 'Color', 'option' => 'BLACK TORTOISE']
	    		]
			],
			[
	    		'sku' => '8053672687583-5',
	    		'regular_price' => '81.58',
	    		'image' => ['id' => $product['images'][2]['id']],
	    		'attributes' => [
	    			['name' => 'Size', 'option' => '47'],
	    			['name' => 'Color', 'option' => 'NAVY BRIGHT BLUE']
	    		]
			]
		]
	];
*/

	$product_id = $product_data['id'];
	$url = "products/$product_id/variations/batch";
	$variant = $woocommerce->post($url, $product->get_variants());

	print_r ($variant);

	} catch (HttpClientException $e) {
	    $e->getMessage(); // Error message.
	    $e->getRequest(); // Last request data.
	    $e->getResponse(); // Last response data.
	}



?>