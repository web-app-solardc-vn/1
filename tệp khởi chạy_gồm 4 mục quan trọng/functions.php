<?php
// Add custom Theme Functions here
//____________________________________________________________________________________________________________
// Thay đổi tên trạng thái đơn hàng
add_filter( 'wc_order_statuses', 'custom_rename_order_statuses' );

function custom_rename_order_statuses( $order_statuses ) {
    if ( isset( $order_statuses['wc-on-hold'] ) ) {
        $order_statuses['wc-on-hold'] = 'Chờ Xác Nhận';
    }
    if ( isset( $order_statuses['wc-pending'] ) ) {
        $order_statuses['wc-pending'] = 'Đang Giao';
    }
    if ( isset( $order_statuses['wc-completed'] ) ) {
        $order_statuses['wc-completed'] = 'Đã Giao';
    }
    // Thêm các thay đổi khác nếu cần
    return $order_statuses;
}

//____________________________________________________________________________________________________________

// Thêm trường tùy chỉnh vào trang chỉnh sửa sản phẩm
add_action( 'woocommerce_product_options_general_product_data', 'them_truong_vo_hieu_cod' );
function them_truong_vo_hieu_cod() {
    woocommerce_wp_checkbox( array(
        'id'          => '_vo_hieu_cod',
        'label'       => __( 'Vô hiệu hoá COD', 'woocommerce' ),
        'description' => __( 'Chọn để tắt thanh toán COD cho sản phẩm này.', 'woocommerce' ),
    ));
}

// Lưu giá trị của trường tùy chỉnh
add_action( 'woocommerce_process_product_meta', 'luu_truong_vo_hieu_cod' );
function luu_truong_vo_hieu_cod( $post_id ) {
    $vo_hieu_cod = isset( $_POST['_vo_hieu_cod'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_vo_hieu_cod', $vo_hieu_cod );
}

// Kiểm tra và loại bỏ COD nếu sản phẩm bị tắt COD
add_filter( 'woocommerce_available_payment_gateways', 'kiem_tra_va_loai_bo_cod', 10, 1 );
function kiem_tra_va_loai_bo_cod( $available_gateways ) {
    if ( is_admin() || ! is_checkout() ) {
        return $available_gateways;
    }

    $cart = WC()->cart->get_cart();
    foreach ( $cart as $cart_item ) {
        $product_id = $cart_item['product_id'];
        $vo_hieu_cod = get_post_meta( $product_id, '_vo_hieu_cod', true );
        if ( 'yes' === $vo_hieu_cod && isset( $available_gateways['cod'] ) ) {
            unset( $available_gateways['cod'] );
            break;
        }
    }

    return $available_gateways;
}


//------------------------------------------------------------------------------------------------------------
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




//-----------------------------------------------------------------------------------------------------------
//cho phép bình luận giống nhau
//add_filter( 'duplicate_comment_id', '__return_false' );(bỏ 2 gạch nếu muốn mở)

//---------------------------------------------------------------------------------------------------------
// đổi ảnh đại diện tài khoản
function custom_user_avatar($avatar, $id_or_email, $size, $default, $alt) {
    // Kiểm tra nếu $id_or_email là ID người dùng
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif (is_string($id_or_email)) {
        // Kiểm tra nếu $id_or_email là email, lấy ID người dùng từ email
        $user = get_user_by('email', $id_or_email);
        $user_id = $user ? $user->ID : null;
    } elseif (is_object($id_or_email)) {
        // Kiểm tra nếu $id_or_email là một đối tượng người dùng
        $user_id = isset($id_or_email->user_id) ? (int) $id_or_email->user_id : null;
    }

    // Nếu tìm thấy ID người dùng, kiểm tra xem người dùng có ảnh đại diện tùy chỉnh không
    if (!empty($user_id)) {
        $user_avatar = get_user_meta($user_id, 'user_avatar', true);

        if ($user_avatar) {
            // Nếu có ảnh đại diện tùy chỉnh, hiển thị ảnh đó
            $avatar = '<img src="' . esc_url($user_avatar) . '" alt="' . esc_attr($alt) . '" class="avatar avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '">';
        }
    }

    return $avatar;
}
add_filter('get_avatar', 'custom_user_avatar', 10, 5);


//------------------------------------------------------------------------------------------------------------
// Thêm ID cho giá chính để có thể cập nhật
function custom_update_main_price_html() {
    global $product;

    if ( $product->is_type( 'variable' ) ) {
        echo '<p class="price" id="main-product-price">' . $product->get_price_html() . '</p>';
    }
}
add_action( 'woocommerce_single_product_summary', 'custom_update_main_price_html', 10 );

// Chèn JavaScript để cập nhật giá chính khi chọn biến thể
function enqueue_update_price_script() {
    if ( is_product() ) {
        ?>
        <script type="text/javascript">
        jQuery(function($) {
            // Lắng nghe sự thay đổi của biến thể
            $('form.variations_form').on('show_variation', function(event, variation) {
                // Cập nhật giá chính (ID: main-product-price) bằng giá của biến thể được chọn
                $('#main-product-price').html(variation.price_html);

                // Áp dụng kích thước nhỏ hơn cho giá khi chọn biến thể
                $('#main-product-price').css({
                    'font-size': '14px', // Điều chỉnh kích thước chữ cho giá biến thể
                    'color': '#e74c3c' // Điều chỉnh màu chữ nếu cần
                });
            });

            // Nếu không có biến thể nào được chọn, quay lại hiển thị giá phạm vi với kích thước mặc định
            $('form.variations_form').on('hide_variation', function() {
                var default_price_html = '<?php global $product; echo $product->get_price_html(); ?>';
                $('#main-product-price').html(default_price_html);

                // Trả lại kích thước chữ mặc định khi không có biến thể được chọn
                $('#main-product-price').css({
                    'font-size': '20px', // Kích thước mặc định
                    'color': '#333' // Màu chữ mặc định
                });
            });
        });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'enqueue_update_price_script' );

//-----------------------------------------------------------------------------------------------------------

//hủy đơn hàng
add_action( 'template_redirect', 'handle_cancel_order_request' );

function handle_cancel_order_request() {
    if ( isset( $_POST['woocommerce_cancel_order'] ) && isset( $_POST['order_id'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-cancel_order' ) ) {
        $order_id = intval( $_POST['order_id'] );
        $order = wc_get_order( $order_id );

        // Kiểm tra xem đơn hàng có thuộc quyền của người dùng hiện tại không
        if ( $order && $order->get_user_id() === get_current_user_id() ) {
            // Kiểm tra trạng thái đơn hàng
            if ( in_array( $order->get_status(), array( 'pending', 'on-hold' ) ) ) {
                // Hủy đơn hàng
                $order->update_status( 'cancelled', __( 'Order cancelled by customer.', 'woocommerce' ) );

                // Thông báo hủy đơn thành công
                wc_add_notice( __( 'Đơn hàng của bạn đã được HỦY.', 'woocommerce' ), 'success' );

                // Tải lại trang chi tiết của đơn hàng
                wp_safe_redirect( wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) ) );
                exit;
            } else {
                wc_add_notice( __( 'You cannot cancel this order.', 'woocommerce' ), 'error' );
            }
        } else {
            wc_add_notice( __( 'Invalid order.', 'woocommerce' ), 'error' );
        }
    }
}

//-------------------------------------------------------------------------------------------------------------

