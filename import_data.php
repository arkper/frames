<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Image.php';
require __DIR__ . '/Attribute.php';
require __DIR__ . '/DefaultAttribute.php';
require __DIR__ . '/Product.php';
require __DIR__ . '/Variant.php';

$DATA_DIR = 'DATA/';
$SOURCE_SITE = 'https://my.luxottica.com/myLuxotticaImages/';

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


// Create (connect to) SQLite database in file
$db = new PDO('sqlite:frames.db') or die ('Could not open the database frames.db');

// Set errormode to exceptions
$db->setAttribute(PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION);
if ($argv[1] == "1") {
	step1();
} elseif ($argv[1] == "2") {
	step2();
} elseif ($argv[1] == "12") {
	step1();
	step2();
}


function step2() {
	global $db;

	$insert_product_sql =
		"insert into products(model, lens_color, lens_material, photochromic, polarized, standard, temple_length, bridge_size," .
		"lens_witdth, lens_height, rx_able, front_color, shape, type, temple_material, front_material, gender, collection, new," .
		"theme, brand_name, brand_code, tags) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

	$insert_variation_sql = "insert into variations(sku, model, color_name, color_code, size, suggested_retail_price, wholesale_price, photo_url) " .
		"values (?,?,?,?,?,?,?,?)";

	//Get distinct list of all models
	$models = $db->query("SELECT distinct model FROM frames");
	//$models = $db->query("SELECT distinct model FROM frames where model='0VO5037'");

	$frames_for_model_statement = $db->prepare("select * from frames where model=?");
	$insert_product_statement = $db->prepare($insert_product_sql);
	$insert_variation_statement = $db->prepare($insert_variation_sql);
	$product_update_statement = $db->prepare("update products set id = ? where model = ?");
	$variant_update_statement = $db->prepare("update variations set id = ? where sku = ?");
	$model_statement = $db->prepare("select id, model from products where model = ?");

	// Load categories
	$categories = getCategories();

	// Load tags
	$tags = getTags();

	foreach ($models as $model_record) {

		try {
			$model = $model_record[0];

			echo "Processing model $model\n";

			$model_statement->execute(array($model));

			$prod_id = $model_statement->fetch(PDO::FETCH_ASSOC)['id'];

			// echo "Product id is $prod_id\n";

			if ($prod_id != null ){
				//echo "Product $prod_id has been updated - skipping\n";
				continue;
			}

			$frames_for_model_statement->execute(array($model));

			$frames_for_model = $frames_for_model_statement->fetchAll(PDO::FETCH_ASSOC);

			// initialize variables before the loop starts
			$variations = array();
			$first_variation = true;
			$position = 0;
			$images = array();
			$attributes = array();
			$default_attributes = array();

			foreach ($frames_for_model as $p) {
				$size = $p['size'];
				$color = $p['color_name'];
				$color_code = $p['color_code'];
				//echo "Size: $size; Color: $color\n";
				if ($first_variation) {

					$first_variation = false;

					try {
						$insert_product_statement->execute(
								array(
										$p['model'],$p['lens_color'],$p['lens_material'],$p['photochromic'],$p['polarized'],$p['standard'],$p['temple_length'],
										$p['bridge_size'],$p['lens_witdth'],$p['lens_height'],$p['rx_able'],$p['front_color'],$p['shape'],$p['type'],$p['temple_material'],
										$p['front_material'],$p['gender'],$categories[$p['collection']],$p['new'],$p['theme'],$p['brand_name'],$p['brand_code'],$tags[$p['brand_name']]
								)
						);
					} catch (Exception $e) {
						echo $e->getMessage() . "\n";
						continue;
					}

					$product = Product::fromArray($p, $categories, $tags);

					array_push($default_attributes, new DefaultAttribute('Color', "$color_code ($color)"));
					array_push($default_attributes, new DefaultAttribute('Size', $size));

				}

				$photo_url = str_replace(' ', '_', $p['model']) . '__' . $p['color_code'] . '_890x445.jpg';
				$photo_url = $SOURCE_SITE . $p['brand_code'] . '/' . str_replace('/', '_', $photo_url);

				try {
					$insert_variation_statement->execute(
						array(
							$p['sku'], $p['model'], $color, $p['color_code'], $size, $p['suggested_retail_price'], $p['wholesale_price'], $photo_url
						)
					);

				} catch (Exception $e) {
					echo $e->getMessage() . "\n";
					continue;
				}

				$image = addImage ($images, $photo_url);
				addAttribute($attributes, Attribute::withNameAndOption('Color', "$color_code ($color)"));
				addAttribute($attributes, Attribute::withNameAndOption('Size', $size));

				$variation = new Variant($p['sku'], $p['suggested_retail_price'], $image,
						array(
								Attribute::withNameAndOption('Color', "$color_code ($color)"), Attribute::withNameAndOption('Size', $size)
						)
				);

				array_push($variations, $variation);

			}

			$product->images = $images;
			$product->attributes = $attributes;
			$product->default_attributes = $default_attributes;
			$product->variants = $variations;

			// Make a call to WP and insert the product
			$product_data = add_product($product);

			//Update the product object with information received from WP (id for product and images)
			$product->update($product_data);

			//Update product record with id from WP
			$product_update_statement->execute(array($product->id, $product->model));

			echo ("Added product " . $product_data['id'] . "\n");

			//echo "Product data:\n";

			//print_r($product_data);

			//Insert product variants in one bulk call to WP
			$variants_data = add_variants($product);

			echo "Variants data:\n";

			print_r($variants_data);

			foreach($variants_data['create'] as $variant) {
				$variant_update_statement->execute(array($variant['id'], $variant['sku']));
			}


			echo "Added product variants\n";

			break;
		} catch (Exception $e) {
			echo $e->getMessage() . "\n";
			continue;

		}
	}
}

function step1() {
	global $db;

	$data_files = array_diff(scandir($DATA_DIR), array('..', '.'));

	$file_name = $data_files[2];

	$count = 0;

	if ($file = fopen($DATA_DIR . $file_name, "r")) {
		fgets($file);
		fgets($file);
	} else {
		die ("Failed to open file $file_name");
	}

	$sql =
		"insert into frames(sku, model, color_name, color_code," .
		"size, lens_color, suggested_retail_price, wholesale_price, lens_material," .
		"photochromic, polarized, standard, temple_length, bridge_size, lens_witdth," .
		"lens_height, rx_able, front_color, shape, type, temple_material," .
		"front_material, gender, collection, new, theme, brand_name, brand_code) " .
		"values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

	$statement = $db->prepare($sql);

	$count = 0;

	$db->query("delete from frames");

	echo "Starting import\n";

	while(!feof($file)) {
		$line = fgets($file);

		$data = explode(";", $line);

		if (!$data[0]) {
			break;
		}

		$result = $statement->execute
		(
			array($data[1],$data[2],$data[3],$data[4],$data[5],$data[7],$data[9],$data[10],$data[12],$data[13],$data[14],
				$data[15],$data[16],$data[17],$data[18],$data[19],$data[23],$data[26],$data[27],$data[28],$data[29],
				$data[30],$data[33],$data[34],$data[36],$data[38],$data[40],$data[41]
			)
		);

		$count++;

		echo "Inserted product $count\n";


	}

}


function getCategories() {
	global $db;
	// Load categories
	$categories = array();

	foreach ($db->query("select name, id from categories")->fetchAll(PDO::FETCH_ASSOC) as $category) {
		$categories[$category['name']] = $category['id'];
	}

	return $categories;
}

function getTags() {
	global $db;
	// Load tags
	$tags = array();
	foreach ($db->query('select name, id from tags')->fetchAll(PDO::FETCH_ASSOC) as $tag) {
		$tags[$tag['name']] = $tag['id'];
	}
	return $tags;
}

function addImage(&$images, $url) {
	$position = 0;
	foreach($images as $image) {
		if ($image->src == $url){
			return $image;
		}
		$position++;
	}
	$image = Image::withSrcAndPosition($url, $position);
	array_push ($images, $image);
	return $image;
}

function addAttribute (&$attributes, $attribute) {
	$position = 0;

	if ($attributes == null) {
		$attributes = array(Attribute::withNameAndPosAndOptions($attribute->name, $position, array($attribute->option)));
		return;
	}

	foreach ($attributes as $attr) {
		if ($attr->name == $attribute->name) {
			// Add an option if needed
			foreach($attr->options as $option) {
				if ($option == $attribute->option) {
					//Option exists - get out now
					return;
				}
			}
			//Option new = add it and get out
			array_push($attr->options, $attribute->option);
			return;
		}
		$position++;
	}
	// Add a new attribute
	array_push ($attributes, Attribute::withNameAndPosAndOptions($attribute->name, $position, array($attribute->option)));

}


function add_product($product) {
	print_r ($product->description() . "\n");

	global $woocommerce;
	return $woocommerce->post('products', $product->getProductData());
}

function add_variants($product) {
	global $woocommerce;
	$product_id = $product->id;

	//echo ("Submitting variants:\n");

	//print_r ($product->getVariantData());

	return $woocommerce->post("products/$product_id/variations/batch", $product->getVariantData());
}


?>