<?php
/**
 * Account header without title or whitespace.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.16.0
 */

$is_facebook_login = is_nextend_facebook_login();
$is_google_login   = is_nextend_google_login();

$login_text     = get_theme_mod( 'facebook_login_text' );
$login_bg_image = get_theme_mod( 'facebook_login_bg', '' );
$login_bg_color = get_theme_mod( 'my_account_title_bg_color', '' );

if ( $login_bg_image ) $css_login_bg_args[] = array(
	'attribute' => 'background-image',
	'value'     => 'url(' . do_shortcode( $login_bg_image ) . ')',
);
if ( $login_bg_color ) $css_login_bg_args[] = array(
	'attribute' => 'background-color',
	'value'     => $login_bg_color,
);

global $wp;
$current_url    = home_url( $wp->request );
?>

<?php if ( is_user_logged_in() ) : ?>
	<!-- Không hiển thị tiêu đề hoặc bất kỳ nội dung nào cho người dùng đã đăng nhập -->
<?php else : ?>
	<div class="social-login text-center">
		<?php if ( $is_facebook_login && get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' && ! is_user_logged_in() ) {
			$facebook_url = add_query_arg( array( 'loginSocial' => 'facebook' ), wp_login_url() );
			?>

			<a href="<?php echo esc_url( $facebook_url ); ?>" class="button social-button large facebook circle" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="475" data-popupheight="175">
				<i class="icon-facebook"></i>
				<span><?php _e( 'Login with <strong>Facebook</strong>', 'flatsome' ); ?></span>
			</a>
		<?php } ?>

		<?php if ( $is_google_login && get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' && ! is_user_logged_in() ) {
			$google_url = add_query_arg( array( 'loginSocial' => 'google' ), wp_login_url() );
			?>

			<a href="<?php echo esc_url( $google_url ); ?>" class="button social-button large google-plus circle" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
				<i class="icon-google-plus"></i>
				<span><?php _e( 'Login with <strong>Google</strong>', 'flatsome' ); ?></span>
			</a>
		<?php } ?>

		<?php if ( $login_text ) { ?><p><?php echo do_shortcode( $login_text ); ?></p><?php } ?>
	</div>
<?php endif; ?>
