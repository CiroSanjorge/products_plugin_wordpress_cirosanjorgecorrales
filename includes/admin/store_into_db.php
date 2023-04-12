<?php
require __DIR__ . '\get_wc_client.php';
require __DIR__ . '\connection.php';
function store_from_woocommerce_into_db(mysqli $connection, string $table, \Automattic\WooCommerce\Client $woo_client): void
{
    $products = $woo_client->get('products');
    $columns = ['id', 'type', 'sku', 'name', 'is_published', 'is_highlighted', 'visibility', 'desc_short', 'desc', 'sale_start_date', 'sale_end_date', 'tax_estate', 'tax_type', 'stock', 'inventory', 'inventory_low_quantity', 'allow_reservation_for_sold_out_products', 'is_sold_individually', 'weight', 'length_cm', 'width_cm', 'height_cm', 'allow_user_reviews', 'buy_note', 'discounted_price', 'price', 'categories', 'tags', 'shippment_type', 'img', 'download_limit', 'download_expiration_days', 'superior', 'grouped_products', 'directed_sales', 'cross_sales', 'external_url', 'button_text', 'position', 'attribute_name_1', 'attribure_value_1', 'visible_attribure_1', 'global_attribure_1'];
    foreach ($products as $key => $product) {
        $p_values = [];
        foreach ($columns as $col) {
            $p_value = $product->$col;
            if ($p_value == "") {
                $p_value = "null";
            }
            //objs y arrays
            if (is_array($p_value) || gettype($p_value) === "object") {
                foreach ($p_value as $arr_values) {
                    if (gettype($arr_values) === "object") {
                        $arr_values = json_decode(json_encode($arr_values), true);
                    }
                }
                array_push($p_values, implode(', ', $arr_values));
                $arr_values = [];
            } else {
                array_push($p_values, $p_value);
            }
        }

        //woocommerce -> db
        $p_values = array_combine($columns, array_values($p_values));
        $query = "INSERT INTO $table VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $sql = mysqli_prepare($connection, $query);
        extract($p_values, EXTR_PREFIX_ALL, "prod");
        $sql->bind_param("isssiisssssssissiissssississssssssssssissss", $prod_id, $prod_type, $prod_sku, $prod_name, $prod_is_published, $prod_is_highlighted, $prod_visibility, $prod_desc_short, $prod_desc, $prod_sale_start_date, $prod_sale_end_date, $prod_tax_estate, $prod_tax_type, $prod_stock, $prod_inventory, $prod_inventory_low_quantity, $prod_allow_reservation_for_sold_out_products, $prod_is_sold_individually, $prod_weight, $prod_length_cm, $prod_width_cm, $prod_height_cm, $prod_allow_user_reviews, $prod_buy_note, $prod_discounted_price, $prod_price, $prod_categories, $prod_tags, $prod_shippment_type, $prod_img, $prod_download_limit, $prod_download_expiration_days, $prod_superior, $prod_grouped_products, $prod_directed_sales, $prod_cross_sales, $prod_external_url, $prod_button_text, $prod_position, $prod_attribute_name_1, $prod_attribure_value_1, $prod_visible_attribure_1, $prod_global_attribure_1);
        $sql->execute();
    }
}
