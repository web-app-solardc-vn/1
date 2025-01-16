<?php
/**
 * My Account Dashboard
 *
 * Shows a simple greeting with the user display name, a "Mua sắm ngay" button, and a "Đăng xuất" button with a confirmation dialog.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);

?>

<p style="text-align: center;">
	<?php
	printf(
		/* translators: 1: user display name */
		wp_kses( __( 'Xin chào %1$s, chúc bạn 1 ngày tốt lành!', 'woocommerce' ), $allowed_html ),
		'<strong style="color: black;">' . esc_html( $current_user->display_name ) . '</strong>'
	);
	?>
</p>

<!-- Nút Mua Sắm Ngay và Đăng Xuất cùng hàng -->
<div style="text-align: center; margin-top: 20px;">
    <!-- Nút Mua Sắm Ngay với viền đen -->
    <a href="https://solarev.com.vn" style="border: 1px solid #000000; background-color: #EEEEEE; color: #000000; padding: 10px 30px; text-decoration: none; font-size: 16px; border-radius: 8px; display: inline-block; margin-right: 10px;">
        MUA SẮM
    </a>

    <!-- Nút Đăng Xuất với viền đen và nền xám -->
    <a href="<?php echo wp_logout_url( wc_get_page_permalink( 'myaccount' ) ); ?>" 
       onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');" 
       style="border: 1px solid #000000; background-color: #EEEEEE; color: #000000; padding: 10px 22px; text-decoration: none; font-size: 16px; border-radius: 8px; display: inline-block;">
        ĐĂNG XUẤT
    </a>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
