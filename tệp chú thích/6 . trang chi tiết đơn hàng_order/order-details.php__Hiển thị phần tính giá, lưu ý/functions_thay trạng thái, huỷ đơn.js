// Thay đổi tên trạng thái đơn hàng
add_filter( 'wc_order_statuses', 'custom_rename_order_statuses' );

function custom_rename_order_statuses( $order_statuses ) {
    if ( isset( $order_statuses['wc-on-hold'] ) ) {
        $order_statuses['wc-on-hold'] = 'Chờ Xác Nhận';
    }
    if ( isset( $order_statuses['wc-pending'] ) ) {
        $order_statuses['wc-pending'] = 'Đang Giao';
    }
    if ( isset( $order_statuses['wc-completed'] ) ) {
        $order_statuses['wc-completed'] = 'Đã Giao';
    }
    // Thêm các thay đổi khác nếu cần
    return $order_statuses;
}



//thêm nút hủy đơn hàng
add_action( 'template_redirect', 'handle_cancel_order_request' );

function handle_cancel_order_request() {
    if ( isset( $_POST['woocommerce_cancel_order'] ) && isset( $_POST['order_id'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-cancel_order' ) ) {
        $order_id = intval( $_POST['order_id'] );
        $order = wc_get_order( $order_id );

        // Kiểm tra xem đơn hàng có thuộc quyền của người dùng hiện tại không
        if ( $order && $order->get_user_id() === get_current_user_id() ) {
            // Kiểm tra trạng thái đơn hàng
            if ( in_array( $order->get_status(), array( 'pending', 'on-hold', 'processing' ) ) ) {
                // Hủy đơn hàng
                $order->update_status( 'cancelled', __( 'Order cancelled by customer.', 'woocommerce' ) );

                // Thông báo hủy đơn thành công
                wc_add_notice( __( 'Đơn hàng của bạn đã được HỦY.', 'woocommerce' ), 'success' );

                // Tải lại trang chi tiết của đơn hàng
                wp_safe_redirect( wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) ) );
                exit;
            } else {
                wc_add_notice( __( 'You cannot cancel this order.', 'woocommerce' ), 'error' );
            }
        } else {
            wc_add_notice( __( 'Invalid order.', 'woocommerce' ), 'error' );
        }
    }
}

// Thêm nút hủy đơn hàng cho đơn hàng tạm giữ, chờ thanh toán hoặc đang xử lý với màu cam và tên 'HỦY ĐƠN HÀNG'
function add_cancel_order_button( $order ) {
    $order_status = $order->get_status();
    if ( in_array( $order_status, array( 'on-hold', 'pending', 'processing' ), true ) ) : ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'woocommerce-cancel_order' ); ?>
            <input type="hidden" name="order_id" value="<?php echo esc_attr( $order->get_id() ); ?>" />
            <button type="submit" name="woocommerce_cancel_order" class="button cancel" style="background-color: #fa4619; color: white;">
                <?php esc_html_e( 'HỦY ĐƠN HÀNG', 'woocommerce' ); ?>
            </button>
        </form>
    <?php endif;
}
add_action( 'woocommerce_order_details_after_order_table', 'add_cancel_order_button' );