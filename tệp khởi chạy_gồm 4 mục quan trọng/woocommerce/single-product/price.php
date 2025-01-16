<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

// Kiểm tra loại sản phẩm và chỉ hiển thị giá cho sản phẩm đơn (simple product)
if ( 'simple' === $product->get_type() ) : 
?>

<!-- Hiển thị giá sản phẩm -->
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
    <?php echo $product->get_price_html(); ?>
</p>

<?php endif; ?>

<!-- Thêm dòng hiển thị số lượng đã bán -->
<?php
// Lấy tổng số lượng sản phẩm đã bán
$total_sales = get_post_meta( $product->get_id(), 'total_sales', true );

// Hiển thị nếu có sản phẩm đã bán
if ( $total_sales ) : ?>
    <p class="sold-count"><?php echo sprintf( __( 'Đã bán: %s', 'woocommerce' ), $total_sales ); ?></p>
<?php endif; ?>
