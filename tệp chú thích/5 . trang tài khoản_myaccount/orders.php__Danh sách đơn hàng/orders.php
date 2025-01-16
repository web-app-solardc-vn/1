<?php
/**
 * Orders
 *
 * Hiển thị đơn hàng trên trang tài khoản.
 *
 * Mẫu này có thể được ghi đè bằng cách sao chép nó vào yourtheme/woocommerce/myaccount/orders.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<!-- Thêm tiêu đề chính "DANH SÁCH ĐƠN HÀNG" -->
<h1 style="
    font-size: 20px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 50px;
    color: #666666;
">
    DANH SÁCH ĐƠN HÀNG
</h1>

<!-- Thêm tiêu đề "ĐƠN HÀNG MỚI" với khung nền xanh dương -->
<h2 style="
    font-size: 15px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 10px;
    background-color: #0073aa; /* Màu xanh dương */
    color: #ffffff; /* Màu chữ trắng */
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    width: auto;
">
    ĐƠN HÀNG MỚI
</h2>

<?php if ( $has_orders ) : ?>

    <!-- Khai báo biến để kiểm tra xem có đơn hàng nào đang xử lý không -->
    <?php $has_processing_orders = false; ?>

    <!-- Lặp qua các đơn hàng và kiểm tra trạng thái -->
    <div class="woocommerce-orders-list">
        <?php
        foreach ( $customer_orders->orders as $customer_order ) {
            $order = wc_get_order( $customer_order );

            // Kiểm tra nếu đơn hàng có trạng thái Đang xử lý, Tạm giữ, Chờ thanh toán
            if ( in_array( $order->get_status(), array( 'processing', 'on-hold', 'pending' ) ) ) {
                $has_processing_orders = true;
                $order_number = $order->get_order_number();
                $order_date = wc_format_datetime( $order->get_date_created() );
                $order_status = wc_get_order_status_name( $order->get_status() );
                $item_count = $order->get_item_count();
                $order_total = $order->get_formatted_order_total();

                $status_color = '';
                if ( $order->has_status( 'processing' ) ) {
                    $status_color = 'color: #04569e;';
                } elseif ( $order->has_status( 'on-hold' ) ) {
                    $status_color = 'color: #ed6e2a;';
                } elseif ( $order->has_status( 'pending' ) ) {
                    $status_color = 'color: #0000FF;';
                }
                ?>

                <hr style="border: 1px solid #000000; margin-bottom: 20px;">

                <div class="woocommerce-order-item" style="display: flex; align-items: flex-start; margin-bottom: 20px; justify-content: space-between;">

                    <div class="order-thumbnail" style="
                        width: 86px;
                        height: 86px;
                        background-color: #f0f0f0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-right: 20px;
                        border: 1px solid orange;
                        border-radius: 6px;
                        flex-direction: column;
                    ">
                        <span style="font-size: 14px; font-weight: bold;"><?php echo esc_html( $order_number ); ?></span>
                        <span style="font-size: 12px; color: #777;">(<?php echo esc_html( $item_count ); ?> mục)</span>
                    </div>

                    <div class="order-details" style="
                        flex-grow: 1;
                        font-size: 14px;
                        margin-right: 20px;
                    ">
                        <span style="font-weight: bold;">Mã đơn hàng: <?php echo esc_html( $order_number ); ?></span><br>
                        <span>Ngày đặt: <?php echo esc_html( $order_date ); ?></span><br>
                        <span>Trạng thái: <span style="<?php echo esc_attr( $status_color ); ?>"><?php echo esc_html( $order_status ); ?></span></span><br>
                        <span style="font-weight: bold;">Tổng: <?php echo wp_kses_post( $order_total ); ?></span><br>
                    </div>

                    <div class="order-actions" style="
                        display: flex;
                        align-items: flex-start;
                        justify-content: center;
                        margin-top: 25px;
                    ">
                        <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View order number %s', 'woocommerce' ), $order_number ) ); ?>" style="
                            display: inline-block;
                            padding: 6px 10px;
                            background-color: #0073aa;
                            color: white;
                            text-decoration: none;
                            border-radius: 5px;
                        ">
                            Xem
                        </a>
                    </div>

                </div>
                <?php
            }
        }
        ?>
    </div>

    <?php if ( ! $has_processing_orders ) : ?>
        <?php wc_print_notice( esc_html__( 'Chưa có đơn hàng nào.', 'woocommerce' ) ); ?>
    <?php endif; ?>

    <h3 style="
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        margin: 50px 0 10px 0;
        background-color: #0073aa;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 6px;
        display: inline-block;
        width: auto;
    ">
        LỊCH SỬ ĐƠN HÀNG
    </h3>

    <div class="woocommerce-orders-history-list">
        <?php
        $has_order_history = false;

        foreach ( $customer_orders->orders as $customer_order ) {
            $order = wc_get_order( $customer_order );

            if ( in_array( $order->get_status(), array( 'cancelled', 'completed', 'refunded', 'failed' ) ) ) {
                $has_order_history = true;
                $order_number = $order->get_order_number();
                $order_date = wc_format_datetime( $order->get_date_created() );
                $order_status = wc_get_order_status_name( $order->get_status() );
                $item_count = $order->get_item_count();
                $order_total = $order->get_formatted_order_total();

                $status_color = '';
                if ( $order->has_status( 'completed' ) ) {
                    $status_color = 'color: green;';
                } elseif ( $order->has_status( 'cancelled' ) ) {
                    $status_color = 'color: #db0404;';
                } elseif ( $order->has_status( 'failed' ) ) {
                    $status_color = 'color: #db0404;';
                } elseif ( $order->has_status( 'refunded' ) ) {
                    $status_color = 'color: purple;';
                }
                ?>

                <hr style="border: 1px solid #000000; margin-bottom: 20px;">

                <div class="woocommerce-order-item" style="
                    display: flex;
                    align-items: flex-start;
                    margin-bottom: 20px;
                    justify-content: space-between;
                ">

                    <div class="order-thumbnail" style="
                        width: 86px;
                        height: 86px;
                        background-color: #f0f0f0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-right: 20px;
                        border: 1px solid orange;
                        border-radius: 6px;
                        flex-direction: column;
                    ">
                        <span style="font-size: 14px; font-weight: bold;"><?php echo esc_html( $order_number ); ?></span>
                        <span style="font-size: 12px; color: #777;">(<?php echo esc_html( $item_count ); ?> mục)</span>
                    </div>

                    <div class="order-details" style="
                        flex-grow: 1;
                        font-size: 14px;
                        margin-right: 20px;
                    ">
                        <span style="font-weight: bold;">Mã đơn hàng: <?php echo esc_html( $order_number ); ?></span><br>
                        <span>Ngày đặt: <?php echo esc_html( $order_date ); ?></span><br>
                        <span>Trạng thái: <span style="<?php echo esc_attr( $status_color ); ?>"><?php echo esc_html( $order_status ); ?></span></span><br>
                        <span style="font-weight: bold;">Tổng: <?php echo wp_kses_post( $order_total ); ?></span><br>
                    </div>

                    <div class="order-actions" style="
                        display: flex;
                        align-items: flex-start;
                        justify-content: center;
                        margin-top: 25px;
                    ">
                        <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View order number %s', 'woocommerce' ), $order_number ) ); ?>" style="
                            display: inline-block;
                            padding: 6px 10px;
                            background-color: #0073aa;
                            color: white;
                            text-decoration: none;
                            border-radius: 5px;
                        ">
                            Xem
                        </a>
                    </div>

                </div>
                <?php
            }
        }

        if ( ! $has_order_history ) {
            wc_print_notice( esc_html__( 'Chưa có lịch sử đơn hàng nào.', 'woocommerce' ) );
        }
        ?>
    </div>

<?php else : ?>

    <?php wc_print_notice( esc_html__( 'Chưa có đơn hàng nào.', 'woocommerce' ) ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>