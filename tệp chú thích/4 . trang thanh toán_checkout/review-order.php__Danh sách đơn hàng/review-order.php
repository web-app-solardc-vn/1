<?php
/**
 * Custom Product Display - Image (62x62), Name, Quantity, Price, Variation Details, and Subtotal
 * (Orange Subtotal)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version Custom
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- Bắt đầu khung tùy chỉnh -->
<div class="custom-order-form" style="border: 1px solid #e3e3d8; padding: 20px; background-color: #f6f6f2;">

    <!-- Phần hiển thị sản phẩm -->
    <div class="product-list">
        <?php 
        $cart_items = WC()->cart->get_cart(); // Lấy tất cả sản phẩm trong giỏ hàng
        $cart_item_count = count( $cart_items ); // Đếm số sản phẩm trong giỏ hàng
        $index = 0; // Biến đếm để theo dõi vị trí sản phẩm
        $total_price = 0; // Biến lưu tổng giá gốc của các sản phẩm

        foreach ( $cart_items as $cart_item_key => $cart_item ) {
            $_product = $cart_item['data']; // Lấy thông tin sản phẩm
            $quantity = $cart_item['quantity']; // Số lượng sản phẩm
            $product_price = $_product->get_price(); // Lấy giá gốc sản phẩm (không bao gồm thuế, phí)
            $subtotal = $product_price * $quantity; // Tính tạm tính (giá * số lượng)
            $total_price += $subtotal; // Cộng dồn vào tổng giá gốc
            
            // Nếu sản phẩm là biến thể, lấy tên của sản phẩm chính
            $product_title = $_product->is_type( 'variation' ) ? $_product->get_parent_data()['title'] : $_product->get_name();
            
            // Lấy các thuộc tính của biến thể nếu là sản phẩm biến thể
            $variation_attributes = $_product->is_type( 'variation' ) ? $_product->get_variation_attributes() : [];
        ?>

        <!-- Mỗi sản phẩm sẽ có ảnh và tên -->
        <div class="product-item" style="display: flex; align-items: flex-start; margin-bottom: 10px; padding-bottom: 10px;">
            <!-- Phần 1: Hiển thị ảnh sản phẩm -->
            <div class="product-image" style="flex-shrink: 0; margin-right: 10px; border-radius: 5px; overflow: hidden;">
                <?php echo $_product->get_image( array( 62, 62 ) ); // Hiển thị ảnh 62x62 với viền bo tròn ?>
            </div>

            <!-- Phần 2: Hiển thị tên sản phẩm và thông tin thêm -->
            <div class="product-name" style="max-width: 100%; word-wrap: break-word; margin-left: 5px; line-height: 1.5; font-size: 1em; font-weight: 300;">
                <!-- Tên sản phẩm chính với màu đen -->
                <strong style="color: #000;"><?php echo wp_kses_post( $product_title ); ?></strong>
                <br>
                
                <!-- Nếu là biến thể, hiển thị chi tiết biến thể trước -->
                <?php if ( $_product->is_type( 'variation' ) && !empty( $variation_attributes ) ) : ?>
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

                <!-- Hiển thị số lượng và giá ở dưới các biến thể -->
                <span><?php echo sprintf( 'SL: %d x %s', $quantity, wc_price( $product_price ) ); ?></span>
                <br>
                
                <!-- Hiển thị Tạm tính (giá x số lượng) với màu cam -->
                <span style="font-weight: 560;color: #339900;">Tạm tính: <?php echo wc_price( $subtotal ); ?></span>
            </div>
        </div>

        <?php 
            $index++; // Tăng biến đếm sau khi hiển thị mỗi sản phẩm

            // Thêm dải phân cách giữa các sản phẩm
            if ( $index < $cart_item_count ) : ?>
                <!-- Dòng phân cách giữa các sản phẩm -->
                <hr style="border: 0.5px solid #000000; margin-bottom: 20px;">
        <?php endif; ?>

        <?php } ?>
    </div>

    <!-- Dải phân cách giữa sản phẩm và cộng tiền hàng -->
    <hr style="border: 1px solid #000000; margin-bottom: 25px;">

    <!-- Phần hiển thị tổng cộng -->
    <div class="order-total" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 1.2em; font-weight: 600;">
        <span style="text-align: left;">Cộng tiền hàng:</span>
        <span style="text-align: right;"><?php echo wc_price( $total_price ); ?></span>
    </div>
</div>
