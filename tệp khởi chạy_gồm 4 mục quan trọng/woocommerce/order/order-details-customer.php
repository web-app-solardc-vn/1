<?php
/**
 * Order Customer Details with Vertical Progress Bar and Status Notes
 *
 * Template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

// Lấy trạng thái hiện tại của đơn hàng
$current_status = $order->get_status();
?>

<style>
    /* Styles cho Thanh Tiến Trình Trục Dọc */
    .order-status-progress-container {
        margin-bottom: 2rem;
    }

    .order-status-progress-container .title {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 1rem;
        color: #333333;
    }

    .order-status-progress {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        position: relative;
    }

    .order-status-progress .step {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
        padding-left: 20px;
    }

    .order-status-progress .step::before {
        content: '';
        width: 12px;
        height: 12px;
        background-color: #ccc;
        border-radius: 50%;
        position: absolute;
        left: 0;
        top: 0;
        z-index: 1;
    }

    .order-status-progress .step.completed::before,
    .order-status-progress .step.active::before {
        background-color: #4caf50;
    }

    .order-status-progress .step::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 12px;
        width: 2px;
        height: calc(100% + 1.5rem - 12px);
        background-color: #ccc;
        z-index: 0;
    }

    .order-status-progress .step:last-child::after {
        display: none;
    }

    .order-status-progress .step.completed::after {
        background-color: #4caf50;
    }

    .order-status-progress .step.active + .step::after {
        background-color: #ccc; /* Màu xám cho thanh nối từ trạng thái hiện tại đến trạng thái tiếp theo */
    }

    .order-status-progress .step .label {
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .order-status-progress .step .note {
        font-size: 0.875rem;
        color: #555555;
    }

    .order-status-progress .step.active .label {
        font-weight: bold;
    }
</style>

<section class="woocommerce-customer-details">

    <!-- Tiêu đề Tình trạng Đơn hàng -->
    <div class="order-status-progress-container">
        <h2 class="title"><?php esc_html_e( 'Tình trạng đơn hàng', 'woocommerce' ); ?></h2>
    
        <!-- Thanh Tiến Trình Trạng Thái Đơn Hàng -->
        <div class="order-status-progress">
            <?php
            // Định nghĩa các trạng thái đơn hàng theo thứ tự tiến trình cùng với chú thích
            $progress_statuses = array(
                'on-hold'    => array(
                    'label' => __( 'Chờ Xác Nhận', 'woocommerce' ),
                    'note'  => __( '[ Đơn hàng của bạn đã được nhận và hiện tại đang chờ hệ thống xử lý ]', 'woocommerce' ),
                ),
                'processing' => array(
                    'label' => __( 'Đang Xử Lý', 'woocommerce' ),
                    'note'  => __( '[ Chúng tôi đang chuẩn bị hàng, đóng gói để bàn giao cho đơn vị vận chuyển ]', 'woocommerce' ),
                ),
                'pending'    => array(
                    'label' => __( 'Đang Giao', 'woocommerce' ),
                    'note'  => __( '[ Đơn vị vận chuyển đã lấy hàng và đang trên đường đến tay bạn. Thời gian giao phụ thuộc vào đơn vị vận chuyển, mong bạn thông cảm và kiên nhẫn chờ đợi. Vui lòng để ý cuộc gọi từ nhân viên giao hàng, xin cảm ơn ]', 'woocommerce' ),
                ),
                'completed'  => array(
                    'label' => __( 'Đã Giao', 'woocommerce' ),
                    'note'  => __( '[ Đơn hàng của bạn đã được giao thành công tới địa chỉ bên dưới, cảm ơn bạn đã tin tưởng và đặt hàng tại solardc.vn ]', 'woocommerce' ),
                ),
            );

            // Lấy độ ưu tiên của các trạng thái đơn hàng
            $order_status_priorities = wc_order_status_priorities();
            $is_current_step = false;

            foreach ( $progress_statuses as $status_key => $status_data ) :
                // Xác định lớp CSS dựa trên trạng thái hiện tại của đơn hàng
                if ( $order->has_status( $status_key ) ) {
                    $step_class = 'step active';
                    $is_current_step = true;
                } elseif ( $is_current_step ) {
                    $step_class = 'step'; // Các bước sau trạng thái hiện tại
                    $is_current_step = false;
                } elseif ( isset( $order_status_priorities[ $status_key ] ) && isset( $order_status_priorities[ $current_status ] ) && $order_status_priorities[ $status_key ] < $order_status_priorities[ $current_status ] ) {
                    $step_class = 'step completed';
                } else {
                    $step_class = 'step';
                }
                ?>
                <div class="<?php echo esc_attr( $step_class ); ?>">
                    <span class="label"><?php echo esc_html( $status_data['label'] ); ?></span>
                    <span class="note"><?php echo esc_html( $status_data['note'] ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tiêu đề Địa chỉ Vận chuyển -->
    <h2 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h2>

    <address>
        <?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

        <?php
            /**
             * Hook sau khi hiển thị địa chỉ trong chi tiết khách hàng đơn hàng.
             *
             * @since 8.7.0
             * @param string $address_type Loại địa chỉ (billing hoặc shipping).
             * @param WC_Order $order Đối tượng đơn hàng.
             */
            do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order );
        ?>
    </address>

    <?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>

<?php
/**
 * Hàm hỗ trợ xác định độ ưu tiên của các trạng thái đơn hàng.
 * Số thấp hơn biểu thị trạng thái sớm hơn trong quy trình.
 *
 * @return array
 */
function wc_order_status_priorities() {
    return array(
        'on-hold'    => 1,
        'processing' => 2,
        'pending'    => 3,
        'completed'  => 4,
    );
}
?>