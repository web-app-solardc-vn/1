// Thêm ID cho giá chính để có thể cập nhật
function custom_update_main_price_html() {
    global $product;

    if ( $product->is_type( 'variable' ) ) {
        echo '<p class="price" id="main-product-price">' . $product->get_price_html() . '</p>';
    }
}
add_action( 'woocommerce_single_product_summary', 'custom_update_main_price_html', 10 );

// Chèn JavaScript để cập nhật giá chính khi chọn biến thể
function enqueue_update_price_script() {
    if ( is_product() ) {
        ?>
        <script type="text/javascript">
        jQuery(function($) {
            // Lắng nghe sự thay đổi của biến thể
            $('form.variations_form').on('show_variation', function(event, variation) {
                // Cập nhật giá chính (ID: main-product-price) bằng giá của biến thể được chọn
                $('#main-product-price').html(variation.price_html);

                // Áp dụng kích thước nhỏ hơn cho giá khi chọn biến thể
                $('#main-product-price').css({
                    'font-size': '14px', // Điều chỉnh kích thước chữ cho giá biến thể
                    'color': '#e74c3c' // Điều chỉnh màu chữ nếu cần
                });
            });

            // Nếu không có biến thể nào được chọn, quay lại hiển thị giá phạm vi với kích thước mặc định
            $('form.variations_form').on('hide_variation', function() {
                var default_price_html = '<?php global $product; echo $product->get_price_html(); ?>';
                $('#main-product-price').html(default_price_html);

                // Trả lại kích thước chữ mặc định khi không có biến thể được chọn
                $('#main-product-price').css({
                    'font-size': '20px', // Kích thước mặc định
                    'color': '#333' // Màu chữ mặc định
                });
            });
        });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'enqueue_update_price_script' );