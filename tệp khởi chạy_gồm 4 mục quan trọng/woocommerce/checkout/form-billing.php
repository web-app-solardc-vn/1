<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
    <?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

        <h3><?php esc_html_e( 'Shipping &amp; Billing', 'woocommerce' ); ?></h3>

    <?php else : ?>

        <h3><?php esc_html_e( 'Shipping details', 'woocommerce' ); ?></h3>

    <?php endif; ?>

    <?php 
    // Thêm phần tóm tắt địa chỉ và nút vào khung nền xám với bo góc
    $current_user = wp_get_current_user();
    $customer_id = $current_user->ID;

    if ( $customer_id ) {
        // Lấy địa chỉ thanh toán
        $billing_address = wc_get_account_formatted_address( 'billing' );

        // Lấy số điện thoại của người dùng
        $billing_phone = get_user_meta( $customer_id, 'billing_phone', true );

        // Nếu có địa chỉ thanh toán hoặc số điện thoại
        if ( $billing_address || $billing_phone ) {
            // Bắt đầu khung nền xám với bo góc
            echo '<div class="billing-summary-box" style="background-color: #f6f6f2; border: 1px solid #e3e3d8; padding: 15px; margin-bottom: 20px; position: relative;">';

            // Hiển thị địa chỉ thanh toán
            echo '<address>' . wp_kses_post( $billing_address ) . '</address>';

            // Hiển thị số điện thoại nếu có
            if ( $billing_phone ) {
                echo '<p>' . esc_html__( 'Sđt:', 'woocommerce' ) . ' ' . esc_html( $billing_phone ) . '</p>';
            }

            // Thêm nút ẩn/hiện các trường thông tin với chữ "Sửa" và căn ra góc phải dưới cùng
            echo '<button type="button" id="toggle-billing-fields" class="button" style="border-radius: 5px;font-size: 12px; background-color: #0073aa; padding: 1px 15px; position: absolute; bottom: 15px; right: 15px;">' . esc_html__( 'Sửa', 'woocommerce' ) . '</button>';
            
            // Kết thúc khung nền xám với bo góc
            echo '</div>';

            // Thêm div chứa các trường thanh toán và mặc định ẩn chúng
            echo '<div id="billing-fields-wrapper" style="display: none;">';
        } else {
            // Nếu không có thông tin địa chỉ, hiển thị các trường thanh toán ngay lập tức
            echo '<div id="billing-fields-wrapper">';
        }
    } else {
        // Nếu người dùng không có ID, hiển thị các trường thanh toán
        echo '<div id="billing-fields-wrapper">';
    }
    ?>

        <div class="woocommerce-billing-fields__field-wrapper">
            <?php
            $fields = $checkout->get_checkout_fields( 'billing' );

            foreach ( $fields as $key => $field ) {
                // Ẩn trường email bằng cách thêm lớp CSS 'hidden'
                if ( 'billing_email' === $key ) {
                    $field['class'][] = 'hidden'; // Thêm lớp CSS vào trường email
                }
                woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
            }
            ?>
        </div>
    </div> <!-- Đóng div billing-fields-wrapper -->

    <?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
    <div class="woocommerce-account-fields">
        <?php if ( ! $checkout->is_registration_required() ) : ?>

            <p class="form-row form-row-wide create-account">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
                </label>
            </p>

        <?php endif; ?>

        <?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

        <?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

            <div class="create-account">
                <?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
                    <?php 
                    // Ẩn trường tạo mật khẩu
                    if ( 'account_password' === $key ) {
                        $field['class'][] = 'hidden'; // Thêm lớp CSS vào trường mật khẩu
                    }
                    woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
                <?php endforeach; ?>
                <div class="clear"></div>
            </div>

        <?php endif; ?>

        <?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
    </div>
<?php endif; ?>

<!-- Thêm JavaScript để ẩn/hiện các trường thông tin thanh toán với hiệu ứng trượt mượt -->
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#toggle-billing-fields').on('click', function() {
            $('#billing-fields-wrapper').slideToggle('slow'); // Hiệu ứng trượt mượt mà khi nhấn nút "Sửa"
        });
    });
</script>
