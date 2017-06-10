<?php
require __DIR__ . '/vendor/autoload.php';

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


while (true) {
	$all_prods = $woocommerce->get('products');
	foreach ($all_prods as $prod) {

		try {
			$pid = $prod['id'];

			echo 'Deleting product id ' . $pid . "\n";
			print_r($woocommerce->delete("products/$pid", ['force' => true]));
		} catch (Exception $e) {
			echo $e->getMessage() . "\n";
			continue;
		}
	}
}




?>