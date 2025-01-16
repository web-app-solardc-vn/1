<?php
/*
Plugin Name: plugin xắp xếp đánh giá______đào thành đạt______
Description: Sắp xếp đánh giá sản phẩm từ mới đến cũ có phân trang, ưu tiên người dùng và giữ nguyên replies
Version: 1.0
Author:ĐÀO THÀNH ĐẠT
*/

if (!defined('ABSPATH')) {
    exit;
}

// Sắp xếp và lấy comments với replies
function get_prioritized_reviews_with_replies($comments, $post_id) {
    if (get_post_type($post_id) === 'product') {
        $current_user_id = get_current_user_id();
        $page = get_query_var('cpage', 1);
        $per_page = get_option('comments_per_page');
        $offset = ($page - 1) * $per_page;

        // Lấy tất cả reviews gốc (không phải replies)
        $parent_reviews = get_comments(array(
            'post_id' => $post_id,
            'status' => 'approve',
            'type' => 'review',
            'order' => 'DESC',
            'orderby' => 'comment_date_gmt',
            'parent' => 0 // Chỉ lấy comments gốc
        ));

        // Tách reviews thành 2 mảng
        $user_reviews = array();
        $other_reviews = array();

        foreach ($parent_reviews as $review) {
            // Lấy tất cả replies cho review này
            $replies = get_comments(array(
                'parent' => $review->comment_ID,
                'status' => 'approve',
                'order' => 'ASC'
            ));
            
            // Thêm replies vào thuộc tính của review gốc
            $review->replies = $replies;

            // Cập nhật tên người bình luận mới nhất
            if ($review->user_id) {
                $user_info = get_userdata($review->user_id);
                if ($user_info) {
                    $review->comment_author = $user_info->display_name;
                    if (user_can($review->user_id, 'administrator')) {
                        $review->comment_author .= ' (🇻🇳Admin)';
                    }
                    if (wc_customer_bought_product($user_info->user_email, $user_info->ID, $post_id)) {
                        $review->comment_author .= ' ( ✅ Khách đã mua)';
                    }
                }
            }

            if ($current_user_id && $review->user_id == $current_user_id) {
                $user_reviews[] = $review;
            } else {
                $other_reviews[] = $review;
            }
        }

        // Gộp reviews với user reviews ở đầu
        $sorted_reviews = array_merge($user_reviews, $other_reviews);

        // Tạo mảng kết quả cuối cùng bao gồm cả replies
        $final_reviews = array();
        foreach (array_slice($sorted_reviews, $offset, $per_page) as $review) {
            $final_reviews[] = $review;
            if (!empty($review->replies)) {
                foreach ($review->replies as $reply) {
                    // Cập nhật tên người bình luận mới nhất cho replies
                    if ($reply->user_id) {
                        $user_info = get_userdata($reply->user_id);
                        if ($user_info) {
                            $reply->comment_author = $user_info->display_name;
                            if (user_can($reply->user_id, 'administrator')) {
                                $reply->comment_author .= ' (Admin🇻🇳)';
                            }
                            if (wc_customer_bought_product($user_info->user_email, $user_info->ID, $post_id)) {
                                $reply->comment_author .= ' ( ✅ Khách đã mua)';
                            }
                        }
                    }
                    $final_reviews[] = $reply;
                }
            }
        }

        return $final_reviews;
    }
    return $comments;
}
add_filter('comments_array', 'get_prioritized_reviews_with_replies', 10, 2);

// Cập nhật số trang dựa trên số lượng parent comments
function adjust_review_pagination($args) {
    if (is_product()) {
        global $post;
        $comments_per_page = get_option('comments_per_page');
        
        // Đếm số lượng reviews gốc (không tính replies)
        $review_count = get_comments(array(
            'post_id' => $post->ID,
            'status' => 'approve',
            'type' => 'review',
            'count' => true,
            'parent' => 0
        ));
        
        $max_pages = ceil($review_count / $comments_per_page);
        $args['total'] = $max_pages;
    }
    return $args;
}
add_filter('woocommerce_comment_pagination', 'adjust_review_pagination');

// Thêm CSS để ảnh và chữ chữ bình luận bé lại__________________________
function add_review_reply_styles() {
    if (is_product()) {
        ?>
        <style>
        .comment.reply {
            margin-left: 50px !important;
            border-left: 2px solid #ddd;
            padding-left: 15px;
        }
        .comment-text * {
            font-size: 12.9px;
        }
        .avatar.avatar-60.photo.lazy-load-active {
            width: 39px; /* Chiều rộng cho ảnh */
            height: 39px; /* Chiều cao cho ảnh */
            border-radius: 50%; /* Bo tròn toàn bộ ảnh */
            object-fit: cover; /* Đảm bảo ảnh lấp đầy khung mà không bị méo */
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'add_review_reply_styles');

// Các function khác giữ nguyên
function modify_wc_reviews_query_with_pagination($args) {
    if (is_product()) {
        $args['order'] = 'DESC';
        $args['orderby'] = 'comment_date_gmt';
        $args['number'] = get_option('comments_per_page');
        $args['paged'] = get_comment_pages_count() > 1 ? get_query_var('cpage', 1) : 1;
    }
    return $args;
}
add_filter('woocommerce_product_review_list_args', 'modify_wc_reviews_query_with_pagination');

function custom_review_pagination($args) {
    if (is_product()) {
        $args['prev_text'] = '&larr;';
        $args['next_text'] = '&rarr;';
        $args['type'] = 'list';
    }
    return $args;
}
add_filter('woocommerce_comment_pagination_args', 'custom_review_pagination');

// Redirect và scroll functions giữ nguyên
function redirect_after_product_review($location, $comment) {
    if (get_post_type($comment->comment_post_ID) === 'product') {
        $product_url = get_permalink($comment->comment_post_ID);
        return $product_url . '#reviews';
    }
    return $location;
}
add_filter('comment_post_redirect', 'redirect_after_product_review', 10, 2);

function add_review_scroll_script() {
    if (is_product()) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            if (window.location.hash === '#reviews') {
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: $('#reviews').offset().top - 100
                    }, 500);
                }, 100);
            }
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'add_review_scroll_script');

// Kiểm tra nếu người dùng đã đánh giá sao cho sản phẩm này
function has_user_rated_product($user_id, $product_id) {
    $comments = get_comments(array(
        'post_id' => $product_id,
        'user_id' => $user_id,
        'type' => 'review',
        'meta_key' => 'rating'
    ));
    return !empty($comments);
}

// Ngăn người dùng đánh giá sao nhiều lần
function restrict_multiple_ratings($commentdata) {
    if (get_post_type($commentdata['comment_post_ID']) === 'product' && isset($_POST['rating'])) {
        $user_id = get_current_user_id();
        if ($user_id && has_user_rated_product($user_id, $commentdata['comment_post_ID'])) {
            wp_die(__('Bạn chỉ có thể đánh giá sao một lần cho mỗi sản phẩm.', 'woocommerce'));
        }
    }
    return $commentdata;
}
add_filter('preprocess_comment', 'restrict_multiple_ratings');

// Thêm nút xoá đánh giá cho đánh giá sao
function add_delete_review_button($comment_text, $comment) {
    if (is_user_logged_in() && get_post_type($comment->comment_post_ID) === 'product' && $comment->user_id == get_current_user_id()) {
        $delete_nonce = wp_create_nonce('delete-comment_' . $comment->comment_ID);
        $delete_url = add_query_arg(array(
            'action' => 'delete_comment',
            'comment_ID' => $comment->comment_ID,
            '_wpnonce' => $delete_nonce
        ), admin_url('comment.php'));
        $comment_text .= '<p><a href="' . esc_url($delete_url) . '" class="delete-review-button">' . __('Xoá nhận xét', 'woocommerce') . '</a></p>';
    }
    return $comment_text;
}
add_filter('comment_text', 'add_delete_review_button', 10, 2);

// Xử lý xoá đánh giá
function handle_delete_review() {
    if (isset($_GET['action']) && $_GET['action'] === 'delete_comment' && isset($_GET['comment_ID']) && isset($_GET['_wpnonce'])) {
        $comment_id = intval($_GET['comment_ID']);
        if (wp_verify_nonce($_GET['_wpnonce'], 'delete-comment_' . $comment_id)) {
            wp_delete_comment($comment_id, true);
            wp_redirect(wp_get_referer());
            exit;
        }
    }
}
add_action('admin_init', 'handle_delete_review');