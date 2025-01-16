<?php
/*
Plugin Name: Đăng nhập mạng xã hội____đào thành đạt_______
Description:đăng nhập mạng xã hội 
Version: 1.0
Author: Đào Thành Đạt
Author URI: Trang web của bạn
Text Domain: product-image-checkout
*/

// Bước 1: Thêm liên kết "Cài đặt" vào trang quản lý plugin
function wgl_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=wgl-settings">Cài đặt</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wgl_add_settings_link');

// Bước 2: Tạo trang cài đặt trong bảng điều khiển WordPress
function wgl_add_settings_page() {
    add_options_page(
        'Google Login Settings', // Tên trang
        'Google Login', // Tên menu
        'manage_options', // Quyền truy cập
        'wgl-settings', // Slug của trang
        'wgl_render_settings_page' // Hàm hiển thị trang
    );
}
add_action('admin_menu', 'wgl_add_settings_page');

// Bước 3: Hiển thị trang cài đặt
function wgl_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Cài Đặt Google và Facebook Login</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wgl-settings-group');
            do_settings_sections('wgl-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Bước 4: Đăng ký các cài đặt
function wgl_register_settings() {
    register_setting('wgl-settings-group', 'wgl_client_id');
    register_setting('wgl-settings-group', 'wgl_client_secret');
    register_setting('wgl-settings-group', 'wgl_redirect_uri');

    add_settings_section('wgl_main_section', 'Google OAuth Settings', null, 'wgl-settings');

    add_settings_field('wgl_client_id', 'Client ID', 'wgl_client_id_callback', 'wgl-settings', 'wgl_main_section');
    add_settings_field('wgl_client_secret', 'Client Secret', 'wgl_client_secret_callback', 'wgl-settings', 'wgl_main_section');
    add_settings_field('wgl_redirect_uri', 'Redirect URI', 'wgl_redirect_uri_callback', 'wgl-settings', 'wgl_main_section');
}
add_action('admin_init', 'wgl_register_settings');

// Bước 5: Hiển thị các trường cài đặt
function wgl_client_id_callback() {
    $client_id = esc_attr(get_option('wgl_client_id'));
    echo '<input type="text" name="wgl_client_id" value="' . $client_id . '" class="regular-text"/>';
}

function wgl_client_secret_callback() {
    $client_secret = esc_attr(get_option('wgl_client_secret'));
    echo '<input type="text" name="wgl_client_secret" value="' . $client_secret . '" class="regular-text"/>';
}

function wgl_redirect_uri_callback() {
    $redirect_uri = esc_attr(get_option('wgl_redirect_uri'));
    echo '<input type="text" name="wgl_redirect_uri" value="' . $redirect_uri . '" class="regular-text"/>';
}

// Bước 6: Sử dụng các cài đặt từ bảng điều khiển trong plugin
function wgl_add_login_buttons() {
    $client_id = esc_attr(get_option('wgl_client_id'));
    $redirect_uri = esc_url(get_option('wgl_redirect_uri'));

    echo '<div class="social-login-buttons">';
    echo '<a href="https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email%20profile" class="google-login-button"><span class="google-login-logo"><img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google logo" /></span><span class="google-login-text">Đăng nhập</span></a>';

    echo '<a href="#" class="facebook-login-button"><span class="facebook-login-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook logo" /></span><span class="facebook-login-text">Đăng nhập</span></a>';
    echo '</div>';
}
add_action('woocommerce_login_form_end', 'wgl_add_login_buttons');

// Bước 7: Thêm CSS cho nút đăng nhập Google và Facebook tùy chỉnh và căn giữa
function wgl_social_login_styles() {
    echo '
    <style>
    .social-login-buttons {
        display: flex;
        justify-content: space-between; /* Đẩy 2 nút sát về 2 bên màn hình */
        gap: 10px; /* Khoảng cách giữa các nút */
        margin-top: 20px; /* Khoảng cách từ trên xuống */
    }
    .google-login-button, .facebook-login-button {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        font-weight: bold;
        border-radius: 3px;
        overflow: hidden;
        border: 2px solid; /* Viền nút */
        flex-grow: 1; /* Đảm bảo nút chiếm đủ chiều rộng có sẵn */
        max-width: 45%; /* Đặt kích thước tối đa để nút không chiếm quá nhiều không gian */
    }
    .google-login-button {
        border-color: #4285f4; /* Viền xanh cho Google */
    }
    .facebook-login-button {
        border-color: #4285f4; /* Viền xanh cho Facebook */
    }
    .google-login-logo, .facebook-login-logo {
        background-color: white;
        padding: 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px; /* Chiều rộng bằng chiều cao để tạo hình vuông */
        height: 40px; /* Đặt chiều cao cho logo */
    }
    .google-login-logo img, .facebook-login-logo img {
        height: 20px;
        width: 20px;
    }
    .google-login-text {
        background-color: #4285f4;
        color: white;
        padding: 10px 15px;
        font-size: 14px;
        flex-grow: 1; /* Đảm bảo phần chữ mở rộng hết phần nền */
    }
    .facebook-login-text {
        background-color: #4285f4;
        color: white;
        padding: 10px 15px;
        font-size: 14px;
        flex-grow: 1; /* Đảm bảo phần chữ mở rộng hết phần nền */
    }
    .google-login-button:hover .google-login-text {
        background-color: #145dbf;
    }
    .facebook-login-button:hover .facebook-login-text {
        background-color: #145dbf;
    }

    /* Responsive styling cho di động */
    @media (max-width: 767px) {
        .social-login-buttons {
            gap: 5px; /* Giảm khoảng cách giữa các nút trên màn hình nhỏ */
        }
        .google-login-button, .facebook-login-button {
            max-width: 48%; /* Điều chỉnh kích thước tối đa để nút không chiếm quá nhiều không gian */
        }
    }
    </style>
    ';
}
add_action('wp_head', 'wgl_social_login_styles');

// Bước 8: Xử lý Google OAuth callback
function wgl_handle_google_login() {
    if (isset($_GET['loginSocial']) && $_GET['loginSocial'] == 'google') {
        if (isset($_GET['code'])) {
            $client_id = esc_attr(get_option('wgl_client_id'));
            $client_secret = esc_attr(get_option('wgl_client_secret'));
            $redirect_uri = esc_url(get_option('wgl_redirect_uri'));
            $code = $_GET['code'];

            // Đổi mã code lấy access token từ Google
            $response = wp_remote_post('https://oauth2.googleapis.com/token', array(
                'body' => array(
                    'code' => $code,
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $redirect_uri,
                    'grant_type' => 'authorization_code',
                ),
            ));

            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['access_token'])) {
                $access_token = $body['access_token'];

                // Lấy thông tin người dùng từ Google
                $response = wp_remote_get('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $access_token);
                $user_info = json_decode(wp_remote_retrieve_body($response), true);

                if (isset($user_info['email'])) {
                    $email = $user_info['email'];
                    $user = get_user_by('email', $email);

                    // Lấy URL ảnh đại diện từ Google
                    $google_avatar_url = isset($user_info['picture']) ? esc_url($user_info['picture']) : '';

                    // Đăng nhập hoặc tạo mới tài khoản
                    if ($user) {
                        wp_set_auth_cookie($user->ID, true); // Thêm true để ghi nhớ đăng nhập
                        if ($google_avatar_url) {
                            update_user_meta($user->ID, 'google_avatar_url', $google_avatar_url);
                        }
                    } else {
                        $user_id = wp_create_user($email, wp_generate_password(), $email);
                        wp_set_auth_cookie($user_id, true); // Thêm true để ghi nhớ đăng nhập
                        if ($google_avatar_url) {
                            update_user_meta($user_id, 'google_avatar_url', $google_avatar_url);
                        }
                    }

                    // Chuyển hướng sau khi đăng nhập thành công đến trang tài khoản
                    wp_redirect(home_url('/tai-khoan'));
                    exit;
                }
            }
        } else {
            // Chuyển hướng nếu không có mã 'code'
            wp_redirect(home_url('/wp-login.php'));
            exit;
        }
    }
}
add_action('init', 'wgl_handle_google_login');

// Bước 9: Hiển thị ảnh đại diện từ Google trong trang tài khoản và trong đánh giá sản phẩm
function wgl_use_google_avatar($avatar, $id_or_email, $size, $default, $alt) {
    $user = false;

    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', $id_or_email);
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
        $user = get_user_by('id', $id_or_email->user_id);
    }

    if ($user) {
        $google_avatar_url = get_user_meta($user->ID, 'google_avatar_url', true);
        if ($google_avatar_url) {
            $avatar = '<img src="' . esc_url($google_avatar_url) . '" alt="' . esc_attr($alt) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" class="avatar avatar-' . esc_attr($size) . ' photo" />';
        }
    }

    return $avatar;
}
add_filter('get_avatar', 'wgl_use_google_avatar', 10, 5);

?>
