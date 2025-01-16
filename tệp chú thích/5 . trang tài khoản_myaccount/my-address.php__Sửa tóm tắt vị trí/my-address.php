<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

// Chỉ lấy địa chỉ thanh toán nhưng thay đổi tiêu đề thành "Địa chỉ giao hàng"
$get_addresses = apply_filters(
    'woocommerce_my_account_get_addresses',
    array(
        'billing' => __( 'Shipping address', 'woocommerce' ), // Đổi tiêu đề thành "Địa chỉ giao hàng"
    ),
    $customer_id
);

$oldcol = 1;
$col    = 1;

foreach ( $get_addresses as $name => $address_title ) : 
    $address = wc_get_account_formatted_address( $name );
    $phone = get_user_meta( $customer_id, 'billing_phone', true ); // Lấy số điện thoại
    $col     = $col * -1;
    $oldcol  = $oldcol * -1;
?>

    <div class="u-column<?php echo $col < 0 ? 1 : 2; ?> col-<?php echo $oldcol < 0 ? 1 : 2; ?> woocommerce-Address">
        <!-- Thêm viền cho khung và tiêu đề -->
        <div style="border: 1px solid #cccccc; padding: 15px; border-radius: 10px; margin-top: 10px; position: relative;">
            <!-- Tiêu đề địa chỉ giao hàng nằm trong khung -->
            <header class="woocommerce-Address-title title" style="margin-bottom: 10px;">
                <h2 style="text-transform: uppercase;"><?php echo esc_html( $address_title ); ?></h2>
            </header>
            <!-- Đưa nút Sửa lên góc phải -->
            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit" style="position: absolute; top: 15px; right: 15px; text-decoration: none;">
                <?php esc_html_e( 'Sửa', 'woocommerce' ); ?>
            </a>
            <address>
                <?php
                    // Hiển thị địa chỉ
                    echo $address ? wp_kses_post( $address ) : esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' );

                    // Hiển thị số điện thoại nếu có
                    if ( $phone ) {
                        echo '<br>' . esc_html__( 'Sđt:', 'woocommerce' ) . ' ' . esc_html( $phone );
                    }
                ?>
            </address>
        </div> <!-- Đóng div khung viền -->
    </div>

<?php endforeach; ?>
