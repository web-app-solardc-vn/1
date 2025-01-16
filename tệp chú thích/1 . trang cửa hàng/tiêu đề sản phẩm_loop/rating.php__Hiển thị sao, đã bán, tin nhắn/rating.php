<?php
/**
 * Loop Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/rating.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( ! wc_review_ratings_enabled() ) {
    return;
}

$average_rating = number_format( floatval( $product->get_average_rating() ), 1 ); // Lấy số sao trung bình và làm tròn đến 1 chữ số thập phân
$review_count = $product->get_review_count(); // Lấy số lượng bình luận
$sold_count = $product->get_total_sales(); // Lấy số lượng sản phẩm đã bán

// Hiển thị biểu tượng ngôi sao, khung bình luận và khung đã bán với số lượng, có khung bao quanh, điều chỉnh khoảng cách và vị trí
echo '<div style="display: flex; align-items: center; margin: 3.8px 0;">'; 
echo '<span class="custom-star-rating" style="display: inline-flex; align-items: center; border: 1px solid #f8ce7c; padding: 0px 2px; border-radius: 5px; background-color: #fdf4e7; margin-right: 3px;">⭐ <span class="average-rating" style="margin-left: 2px; position: relative;">' . esc_html( $average_rating ) . '</span></span>';
echo '<span class="custom-review-count" style="display: inline-flex; align-items: center; border: 1px solid #dee0da; padding: 0px 3px; border-radius: 5px; margin-right: 3px;">💬 <span class="review-count" style="margin-left: 2px; position: relative;">' . esc_html( $review_count ) . '</span></span>';
echo '<span class="custom-sold-count" style="display: inline-flex; align-items: center; border: 1px solid #dee0da; padding: 0px 3px; border-radius: 5px;">Đã bán <span class="sold-count" style="margin-left: 2px; position: relative;">' . esc_html( $sold_count ) . '</span></span>';
echo '</div>';
?>
