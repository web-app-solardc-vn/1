<?php
/**
 * Hình ảnh sản phẩm đơn
 *
 * Mẫu này có thể được ghi đè bằng cách sao chép nó vào yourtheme/woocommerce/single-product/product-image.php.
 *
 * @see              https://docs.woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          9.1.0
 * @flatsome-version 3.19.4
 */

defined( 'ABSPATH' ) || exit;

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
    'woocommerce-product-gallery',
    'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
    'woocommerce-product-gallery--columns-' . absint( $columns ),
    'images',
) );

$slider_classes = array('product-gallery-slider', 'slider', 'slider-nav-small', 'mb-half');

// Phóng to hình ảnh
if (get_theme_mod('product_zoom', 0)) {
    $slider_classes[] = 'has-image-zoom';
}

$rtl = is_rtl() ? 'true' : 'false';

if (get_theme_mod('product_gallery_slider_type') === 'fade') {
    $slider_classes[] = 'slider-type-fade';
}

if (get_theme_mod('product_lightbox', 'default') == 'disabled') {
    $slider_classes[] = 'disable-lightbox';
}
?>
<?php do_action('flatsome_before_product_images'); ?>

<div class="product-images relative mb-half has-hover <?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">

    <?php do_action('flatsome_sale_flash'); ?>

    <div class="image-tools absolute top show-on-hover right z-3">
        <?php do_action('flatsome_product_image_tools_top'); ?>
    </div>

    <div class="woocommerce-product-gallery__wrapper <?php echo esc_attr( implode(' ', $slider_classes) ); ?>" 
        data-flickity-options='{
                "cellAlign": "center",
                "wrapAround": true,
                "autoPlay": false,
                "prevNextButtons": true,
                "adaptiveHeight": true,
                "imagesLoaded": true,
                "lazyLoad": 1,
                "dragThreshold": 15,
                "pageDots": false,
                "rightToLeft": <?php echo $rtl; ?>
       }'>

        <?php
        // Hiển thị ảnh đại diện sản phẩm
        if ($product->get_image_id()) {
            $html  = flatsome_wc_get_gallery_image_html($post_thumbnail_id, true);
        } else {
            $wrapper_classname = $product->is_type('variable') && !empty($product->get_available_variations('image')) ?
                'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
                'woocommerce-product-gallery__image--placeholder';
            $html = sprintf('<div class="%s">', esc_attr($wrapper_classname));
            $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'woocommerce'));
            $html .= '</div>';
        }

        echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id); // Hiển thị ảnh đại diện

        // Kiểm tra nếu sản phẩm có URL video Vimeo
        $vimeo_url = get_post_meta($product->get_id(), '_vimeo_video_url', true);

        if ($vimeo_url) {
            // Lấy video ID từ URL Vimeo
            $vimeo_id = (int) substr(parse_url($vimeo_url, PHP_URL_PATH), 1);

            if ($vimeo_id) {
                // Hiển thị video Vimeo không tự động phát và không lặp
                echo '<div class="woocommerce-vimeo-video-wrapper" style="position: relative; padding-bottom: 100%; height: 0; overflow: hidden; max-width: 100%; background: #000;">';
                echo '<iframe src="https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '?title=0&byline=0&portrait=0" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>';
                echo '</div>';
            }
        }

        do_action('woocommerce_product_thumbnails');
        ?>
    </div>

    <!-- Xóa công cụ hình ảnh ở dưới góc trái -->
    <!-- <div class="image-tools absolute bottom left z-3">
        <?php do_action('flatsome_product_image_tools_bottom'); ?>
    </div> -->
</div>

<?php do_action('flatsome_after_product_images'); ?>

<style>
    /* Ẩn các nút điều hướng (mũi tên) */
    .flickity-button {
        display: none !important;
    }
</style>

<script>
    // Không còn các sự kiện liên quan đến lớp phủ
</script>

<?php wc_get_template('woocommerce/single-product/product-gallery-thumbnails.php'); ?>