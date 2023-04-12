<?php

require __DIR__ . '\..\vendor\autoload.php';

use Automattic\WooCommerce\Client;

function get_woocommerce_client($url_API_woo, $ck_API_woo, $cs_API_woo)
{
    $wc = new Client(
        $url_API_woo,
        $ck_API_woo,
        $cs_API_woo,
        ['version' => 'wc/v3']
    );
    return $wc;
}
//Crear cliente woocommerce 
