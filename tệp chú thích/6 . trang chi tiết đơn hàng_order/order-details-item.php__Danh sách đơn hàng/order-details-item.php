<?php
/**
 * Order Item Details for Order
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

// Lấy thông tin sản phẩm từ đơn hàng
$product = $item->get_product();
$quantity = $item->get_quantity(); // Lấy số lượng sản phẩm
$regular_price = $product->get_regular_price(); // Lấy giá gốc của sản phẩm
$sale_price = $product->get_sale_price(); // Lấy giá giảm nếu có

// Nếu sản phẩm có giá giảm, sử dụng giá giảm; nếu không thì sử dụng giá gốc
$display_price = $sale_price ? $sale_price : $regular_price;
$subtotal = $display_price * $quantity; // Tính tổng tiền dựa trên giá đã hiển thị (giá giảm nếu có)

// Nếu sản phẩm là biến thể, lấy tên của sản phẩm chính
$product_title = $product->is_type( 'variation' ) ? $product->get_parent_data()['title'] : $product->get_name();

// Lấy các thuộc tính của biến thể nếu là sản phẩm biến thể
$variation_attributes = $product->is_type( 'variation' ) ? $product->get_variation_attributes() : [];

// Lấy URL của sản phẩm
$product_url = $product->get_permalink();
?>

<!-- Bắt đầu khung tùy chỉnh -->
<div class="custom-order-form">

    <!-- Dải phân cách ngay dưới tiêu đề (không cần tiêu đề) -->
    <hr style="border: 1px solid #000000; margin-bottom: 20px;">

    <!-- Phần hiển thị sản phẩm -->
    <div class="product-list">

        <!-- Mỗi sản phẩm sẽ có ảnh và tên -->
        <div class="product-item" style="display: flex; align-items: flex-start; margin-bottom: 10px; padding-bottom: 10px;">
            <!-- Phần 1: Hiển thị ảnh sản phẩm -->
            <div class="product-image" style="flex-shrink: 0; margin-right: 10px; border-radius: 5px; overflow: hidden;">
                <?php echo $product->get_image( array( 62, 62 ) ); // Hiển thị ảnh 62x62 với viền bo tròn ?>
            </div>

            <!-- Phần 2: Hiển thị tên sản phẩm và thông tin thêm -->
            <div class="product-name" style="max-width: 100%; word-wrap: break-word; margin-left: 5px; line-height: 1.5; font-size: 1em; font-weight: 300;">
                <!-- Tên sản phẩm chính với màu xanh ngả đen -->
                <strong style="color: #003300;">
                    <a href="<?php echo esc_url( $product_url ); ?>" style="color: #003300; text-decoration: none;">
                        <?php echo wp_kses_post( $product_title ); ?>
                    </a>
                </strong>
                <br>
                
                <!-- Nếu là biến thể, hiển thị chi tiết biến thể trước -->
                <?php if ( $product->is_type( 'variation' ) && !empty( $variation_attributes ) ) : ?>
                    <?php foreach ( $variation_attributes as $attribute_name => $attribute_value ) : ?>
                        <span style="font-size: 1em; color: #888;">
                            <?php 
                                // Lấy nhãn của thuộc tính
                                $attribute_label = wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ) );
                                // Hiển thị nhãn thuộc tính với màu xám và giá trị thuộc tính với màu đen
                                echo esc_html( $attribute_label ) . ': ';
                            ?>
                        </span>
                        <span style="font-size: 1em; color: #000; font-weight: 800;">
                            <?php echo esc_html( $attribute_value ); // Giá trị thuộc tính (màu đen) ?>
                        </span>
                        <br>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Hiển thị số lượng và giá (giá giảm nếu có) của sản phẩm -->
                <span><?php echo sprintf( 'SL: %d x %s', $quantity, wc_price( $display_price ) ); ?></span>
                <br>
                
                <!-- Hiển thị Tạm tính (giá đã giảm x số lượng) với màu cam -->
                <span style="font-weight: 560; color: #339900;">Tạm tính: <?php echo wc_price( $subtotal ); ?></span>
                
                <!-- Nếu sản phẩm có giảm giá, hiển thị giá gốc với màu đỏ gạch chân -->
                <?php if ( $sale_price ) : ?>
                    <br>
                    <span style="text-decoration: line-through; color: #ff0000;">Giá gốc: <?php echo wc_price( $regular_price ); ?></span>
                <?php endif; ?>
            </div>
        </div>

    </div> <!-- Kết thúc phần hiển thị sản phẩm -->
</div> <!-- Kết thúc khung tùy chỉnh -->
