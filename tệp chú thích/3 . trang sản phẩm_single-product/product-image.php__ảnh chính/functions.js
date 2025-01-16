// Thêm Meta Box riêng cho trường Vimeo Video URL
add_action('add_meta_boxes', 'add_vimeo_video_meta_box');

function add_vimeo_video_meta_box() {
    add_meta_box(
        'vimeo_video_meta_box', // ID của meta box
        __('Vimeo Video URL', 'woocommerce'), // Tiêu đề của meta box
        'display_vimeo_video_meta_box', // Callback hiển thị nội dung của meta box
        'product', // Loại bài post (sản phẩm)
        'normal', // Vị trí của meta box ('normal', 'side', 'advanced')
        'high' // Ưu tiên hiển thị (độ cao hiển thị trong trang)
    );
}

// Hiển thị nội dung trong meta box
function display_vimeo_video_meta_box($post) {
    // Thêm nonce để bảo mật
    wp_nonce_field('save_vimeo_video_meta_box', 'vimeo_video_meta_box_nonce');

    $vimeo_video_url = get_post_meta($post->ID, '_vimeo_video_url', true);
    ?>
    <label for="vimeo_video_url"><?php _e('Vimeo Video URL', 'woocommerce'); ?></label>
    <input type="text" id="vimeo_video_url" name="_vimeo_video_url" value="<?php echo esc_attr($vimeo_video_url); ?>" placeholder="https://vimeo.com/..." style="width: 100%;">
    <?php
}

// Lưu URL video Vimeo vào cơ sở dữ liệu khi lưu sản phẩm
add_action('save_post', 'save_vimeo_url_meta_box');

function save_vimeo_url_meta_box($post_id) {
    // Kiểm tra nonce để đảm bảo tính hợp lệ
    if (!isset($_POST['vimeo_video_meta_box_nonce']) || !wp_verify_nonce($_POST['vimeo_video_meta_box_nonce'], 'save_vimeo_video_meta_box')) {
        return;
    }

    // Kiểm tra quyền chỉnh sửa bài viết
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Kiểm tra và lưu URL video Vimeo nếu có
    if (isset($_POST['_vimeo_video_url'])) {
        $vimeo_url = sanitize_text_field($_POST['_vimeo_video_url']);
        
        // Xác thực URL Vimeo (sử dụng regex hoặc hàm tùy chọn)
        if (filter_var($vimeo_url, FILTER_VALIDATE_URL) && strpos($vimeo_url, 'vimeo.com') !== false) {
            update_post_meta($post_id, '_vimeo_video_url', esc_url($vimeo_url));
        } else {
            // Nếu URL không hợp lệ, có thể xóa meta hoặc xử lý theo cách khác
            delete_post_meta($post_id, '_vimeo_video_url');
        }
    }
}
