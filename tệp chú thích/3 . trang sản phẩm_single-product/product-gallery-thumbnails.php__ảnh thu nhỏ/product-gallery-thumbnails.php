<?php
/**
 * Ảnh thu nhỏ của thư viện sản phẩm với tỉ lệ 1:1 cho video Vimeo.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.16.0
 */

global $post, $product;

$attachment_ids = $product->get_gallery_image_ids();
$post_thumbnail = has_post_thumbnail();
$thumb_count    = count( $attachment_ids );

// Kiểm tra xem sản phẩm có video Vimeo không
$vimeo_url = get_post_meta( $product->get_id(), '_vimeo_video_url', true );
if ( $vimeo_url ) {
    $thumb_count++; // Thêm video vào bộ đếm ảnh thu nhỏ
}

if ( $post_thumbnail ) $thumb_count++;
$render_without_attachments = apply_filters( 'flatsome_single_product_thumbnails_render_without_attachments', false, $product, array( 'thumb_count' => $thumb_count ) );

// Tắt ảnh thu nhỏ nếu chỉ có một hình ảnh duy nhất.
if ( $post_thumbnail && $thumb_count == 1 && ! $render_without_attachments ) {
	return;
}

$rtl              = 'false';
$thumb_cell_align = 'left';

if ( is_rtl() ) {
	$rtl              = 'true';
	$thumb_cell_align = 'right';
}

if ( $attachment_ids || $render_without_attachments ) {
	$loop          = 0;
	$image_size    = 'thumbnail';
	$gallery_class = array( 'product-thumbnails', 'thumbnails' );

	// Kiểm tra xem có kích thước ảnh thu nhỏ tùy chỉnh không và sử dụng nó.
	$image_check = wc_get_image_size( 'gallery_thumbnail' );
	if ( $image_check['width'] !== 100 ) {
		$image_size = 'gallery_thumbnail';
	}

	$gallery_thumbnail = wc_get_image_size( apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_' . $image_size ) );

	if ( $thumb_count < 5 ) {
		$gallery_class[] = 'slider-no-arrows';
	}

	$gallery_class[] = 'slider row row-small row-slider slider-nav-small small-columns-4';
	$gallery_class   = apply_filters( 'flatsome_single_product_thumbnails_classes', $gallery_class );
	?>
	<div class="<?php echo implode( ' ', $gallery_class ); ?>"
		data-flickity-options='{
			"cellAlign": "<?php echo $thumb_cell_align; ?>",
			"wrapAround": false,
			"autoPlay": false,
			"prevNextButtons": true,
			"asNavFor": ".product-gallery-slider",
			"percentPosition": true,
			"imagesLoaded": true,
			"pageDots": false,
			"rightToLeft": <?php echo $rtl; ?>,
			"contain": true
		}'>
		<?php

		// Hiển thị ảnh chính của sản phẩm
		if ( $post_thumbnail ) :
			?>
			<div class="col is-nav-selected first">
				<a>
					<?php
					$image_id  = get_post_thumbnail_id( $post->ID );
					$image     = wp_get_attachment_image_src( $image_id, apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_' . $image_size ) );
					$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					$image     = '<img src="' . $image[0] . '" alt="' . $image_alt . '" width="' . $gallery_thumbnail['width'] . '" height="' . $gallery_thumbnail['height'] . '" class="attachment-woocommerce_thumbnail" />';

					echo $image;
					?>
				</a>
			</div><?php
		endif;

		// Thêm ảnh thu nhỏ của video Vimeo sau ảnh chính
		if ( $vimeo_url ) :
			?>
			<div class="col vimeo-thumbnail" style="position: relative;">
				<a style="display: block; width: 100%;">
					<?php
					// Lấy ID video Vimeo và tạo URL cho ảnh thu nhỏ mặc định
					$vimeo_id = (int) substr(parse_url($vimeo_url, PHP_URL_PATH), 1);
					$vimeo_thumbnail_url = 'https://vumbnail.com/' . $vimeo_id . '.jpg';
					?>
					<div style="width: 186%; padding-top: 96%; position: relative;">
						<img src="<?php echo esc_url( $vimeo_thumbnail_url ); ?>" alt="Vimeo video thumbnail" style="width: 100%; height: 110%; position: absolute; top: 0; left: 0; object-fit: cover;" class="attachment-woocommerce_thumbnail" />
					</div>
					<!-- Thêm biểu tượng video -->
					<div class="video-icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 38px; height: 38px; background: rgba(0, 0, 0, 0.6); border-radius: 50%; display: flex; justify-content: center; align-items: center;">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px">
							<path d="M8 5v14l11-7z"/>
						</svg>
					</div>
				</a>
			</div>
		<?php
		endif;

		// Hiển thị các ảnh thu nhỏ khác của sản phẩm
		foreach ( $attachment_ids as $attachment_id ) {

			$classes     = array( '' );
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image       = wp_get_attachment_image_src( $attachment_id, apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_' . $image_size ) );

			if ( empty( $image ) ) {
				continue;
			}

			$image_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$image     = '<img src="' . $image[0] . '" alt="' . $image_alt . '" width="' . $gallery_thumbnail['width'] . '" height="' . $gallery_thumbnail['height'] . '"  class="attachment-woocommerce_thumbnail" />';

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="col"><a>%s</a></div>', $image ), $attachment_id, $post->ID, $image_class );

			$loop ++;
		}
		?>
	</div>
	<?php
} ?>