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


//$category=new Category(25);

$data = [
	'name' => 'Dolce &amp; Gabbana'
];

print_r($woocommerce->post('products/tags', $data));


?>