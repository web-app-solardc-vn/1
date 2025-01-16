<?php
/**
 * Account links.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.19.6
 */

?>
<?php if ( has_nav_menu( 'my_account' ) ) { ?>
	<?php
	echo wp_nav_menu( array(
		'theme_location' => 'my_account',
		'container'      => false,
		'items_wrap'     => '%3$s',
		'depth'          => 0,
		'walker'         => new FlatsomeNavSidebar,
	) );
	?>
<?php } elseif ( ! get_theme_mod( 'wc_account_links', 1 ) ) { ?>
	<li>Define your My Account dropdown menu in <b>Appearance > Menus</b> or enable default WooCommerce Account Endpoints.</li>
<?php } ?>

<?php if ( function_exists( 'wc_get_account_menu_items' ) && get_theme_mod( 'wc_account_links', 1 ) ) { ?>
	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<?php if ( $endpoint !== 'customer-logout' && $endpoint !== 'edit-account' ) { // Loại bỏ mục Thoát và Tài khoản ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<?php if ( $endpoint == 'dashboard' ) { ?>
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
						<?php echo ( fl_woocommerce_version_check( '9.3' ) && wc_is_current_account_menu_item( $endpoint ) ) ? 'aria-current="page"' : ''; ?>>
						<?php echo esc_html( $label ); ?>
					</a>
				<?php } else { ?>
					<a href="<?php echo esc_url( wc_get_endpoint_url( $endpoint, '', wc_get_page_permalink( 'myaccount' ) ) ); ?>"
						<?php echo ( fl_woocommerce_version_check( '9.3' ) && wc_is_current_account_menu_item( $endpoint ) ) ? 'aria-current="page"' : ''; ?>>
						<?php echo esc_html( $label ); ?>
					</a>
				<?php } ?>
			</li>
		<?php } ?>
	<?php endforeach; ?>
	<?php do_action( 'flatsome_account_links' ); ?>
	<!-- Xóa mục Logout -->
<?php } ?>
