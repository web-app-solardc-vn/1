<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( $product->is_type( 'variable' ) ) {
    // Sản phẩm biến thể, lấy giá thấp nhất
    $min_price = $product->get_variation_price( 'min', true );
    echo '<span class="price">Chỉ từ ' . wc_price( $min_price ) . '</span>';
} else {
    // Sản phẩm không biến thể, hiển thị giá bình thường
    if ( $price_html = $product->get_price_html() ) {
        echo '<span class="price">' . $price_html . '</span>';
    }
}
?>
