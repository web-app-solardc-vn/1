<?php
/**
 * Plugin Name: Thêm nút liên hệ______đào thành đạt_________
 * Description: Plugin hiển thị một nút liên hệ nổi trên trang web.
 * Version: 1.0
 * Author: Đào Thành Đạt
 * Author URI: solarev.com.vn
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Ngăn chặn truy cập trực tiếp
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Enqueue Font Awesome để sử dụng các biểu tượng
function fcb_enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'fcb_enqueue_font_awesome');

// Thêm kiểu CSS trực tiếp từ PHP
function fcb_enqueue_styles() {
    echo '
    <style>
        /* Định nghĩa animation cho hiệu ứng tỏa sáng */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7); /* Hiệu ứng tỏa màu đỏ */
            }
            70% {
                box-shadow: 0 0 30px 30px rgba(255, 0, 0, 0); /* Giảm dần độ mờ */
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 0, 0, 0); /* Kết thúc tỏa sáng */
            }
        }

        /* Container cho nút liên hệ */
        .fcb-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 10001; /* Đặt trên lớp phủ */
        }

        /* Lớp phủ phía sau nút liên hệ khi mở rộng */
        .fcb-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000; /* Dưới các nút nhưng trên các nội dung khác */
            display: none;
        }

        /* Định nghĩa kiểu cho nút chính */
        .fcb-button {
            display: block;
            width: 60px;
            height: 60px;
            background-color: #ff0000; /* Nền nút liên hệ */
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 50%;
            transition: background-color 0.3s ease;
            line-height: 60px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border: 2px solid #00ff00; /* Viền xanh lá cây */
            animation: pulse 2s infinite; /* Thêm animation cho nút */
        }

        /* Hiệu ứng hover cho nút liên hệ */
        .fcb-button:hover {
            background-color: #cc0000;
        }

        /* Kiểu cho nút khi ở trạng thái đóng (hiển thị dấu X) */
        .fcb-button.fcb-close {
            background-color: #ffffff; /* Nền nút khi hiển thị dấu X */
            color: #ff0000; /* Màu chữ khi hiển thị dấu X */
        }

        /* Định nghĩa kiểu cho văn bản hiển thị trên nút */
        .fcb-button .fcb-text {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        /* Ẩn chữ "Liên hệ" khi nút trở thành dấu X */
        .fcb-button.fcb-close .fcb-text {
            opacity: 0;
        }

        /* Định nghĩa kiểu cho dấu X */
        .fcb-button .fcb-x {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Hiển thị dấu X khi nút được kích hoạt */
        .fcb-button.fcb-close .fcb-x {
            opacity: 1;
        }

        /* Định nghĩa kiểu cho các nút mở rộng */
        .fcb-expand-button {
            position: absolute;
            right: 0;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
            transform: translateY(20px);
            display: block;
            width: 60px;
            height: 60px;
            background-color: #ffffff; /* Nền màu trắng cho nút mở rộng */
            border-radius: 50%;
            overflow: hidden;
            color: #ff0000; /* Màu chữ của nút mở rộng */
        }

        /* Hiển thị các nút mở rộng khi container ở trạng thái mở rộng */
        .fcb-container.expanded .fcb-expand-button {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Định nghĩa kiểu cho hình ảnh trong nút mở rộng */
        .fcb-expand-button img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Đặt vị trí cho các nút mở rộng theo thứ tự từ dưới lên */
        .fcb-expand-button:nth-child(2) {
            bottom: 80px;
        }

        .fcb-expand-button:nth-child(3) {
            bottom: 150px;
        }

        .fcb-expand-button:nth-child(4) {
            bottom: 220px;
        }

        .fcb-expand-button:nth-child(5) {
            bottom: 290px;
        }

        /* Hiển thị lớp phủ khi mở rộng */
        .fcb-container.expanded + .fcb-overlay {
            display: block;
        }
    </style>
    ';
}
add_action('wp_head', 'fcb_enqueue_styles');

// Thêm JavaScript để xử lý sự kiện click
function fcb_enqueue_scripts() {
    echo '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var fcbButton = document.querySelector(".fcb-button");
            var fcbContainer = document.querySelector(".fcb-container");
            var fcbOverlay = document.querySelector(".fcb-overlay");

            // Xử lý sự kiện click vào nút chính để mở rộng hoặc thu gọn các nút
            fcbButton.addEventListener("click", function() {
                fcbContainer.classList.toggle("expanded");
                fcbButton.classList.toggle("fcb-close");
                fcbOverlay.classList.toggle("active");
            });

            // Xử lý sự kiện click vào lớp phủ để thu gọn các nút
            fcbOverlay.addEventListener("click", function() {
                fcbContainer.classList.remove("expanded");
                fcbButton.classList.remove("fcb-close");
                fcbOverlay.classList.remove("active");
            });
        });
    </script>
    ';
}
add_action('wp_footer', 'fcb_enqueue_scripts');

// Hiển thị nút liên hệ nổi, lớp phủ và các nút mở rộng
function fcb_display_contact_button() {
    $plugin_url = plugin_dir_url(__FILE__);
    $options = get_option('fcb_settings');
    
    $link_a = isset($options['fcb_link_a']) ? 'https://m.me/' . esc_attr($options['fcb_link_a']) : '#';
    $link_b = isset($options['fcb_link_b']) ? esc_url($options['fcb_link_b']) : '#';
    $link_c = isset($options['fcb_link_c']) ? 'tel:' . esc_attr($options['fcb_link_c']) : '#';
    $link_d = isset($options['fcb_link_d']) ? esc_url('https://www.google.com/maps/search/?api=1&query=' . urlencode($options['fcb_link_d'])) : '#';

    echo '
    <div class="fcb-container">
        <div class="fcb-button">
            <div class="fcb-text">Liên hệ</div>
            <div class="fcb-x"><i class="fas fa-times"></i></div>
        </div>
        <a href="'.esc_url($link_a).'" class="fcb-expand-button"><img src="'.esc_url($plugin_url.'3.jpg').'" alt="Button 1" /></a>
        <a href="'.esc_url($link_b).'" class="fcb-expand-button"><img src="'.esc_url($plugin_url.'1.jpg').'" alt="Button 2" /></a>
        <a href="'.esc_url($link_c).'" class="fcb-expand-button"><img src="'.esc_url($plugin_url.'2.jpg').'" alt="Button 3" /></a>
        <a href="'.esc_url($link_d).'" class="fcb-expand-button"><img src="'.esc_url($plugin_url.'4.jpg').'" alt="Button 4" /></a>
    </div>
    <div class="fcb-overlay"></div>';
}
add_action('wp_footer', 'fcb_display_contact_button');



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Trang cài đặt plugin
function fcb_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=fcb_settings">Cài đặt</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'fcb_add_settings_link');

// Tạo trang cài đặt trong quản trị viên
function fcb_create_settings_page() {
    add_options_page(
        'Floating Contact Button Settings', // Tiêu đề trang
        'Floating Contact Button', // Tên hiển thị trong menu
        'manage_options', // Quyền cần thiết để truy cập
        'fcb_settings', // Slug trang
        'fcb_settings_page_html' // Hàm hiển thị nội dung trang
    );
}
add_action('admin_menu', 'fcb_create_settings_page');

function fcb_settings_page_html() {
    // Kiểm tra quyền quản trị
    if (!current_user_can('manage_options')) {
        return;
    }

    // Nội dung trang cài đặt
    echo '<div class="wrap">';
    echo '<h1>Cài đặt Floating Contact Button</h1>';
    echo '<form action="options.php" method="post">';
    // Hiển thị các thiết lập của plugin
    settings_fields('fcb_settings_group');
    do_settings_sections('fcb_settings');
    submit_button('Lưu cài đặt');
    echo '</form>';
    echo '</div>';
}

// Đăng ký các thiết lập cho trang cài đặt
function fcb_register_settings() {
    register_setting('fcb_settings_group', 'fcb_settings');
    
    add_settings_section(
        'fcb_settings_section', // ID của section
        'Cài đặt chính', // Tiêu đề section
        null, // Mô tả section (có thể bỏ trống)
        'fcb_settings' // Slug của trang chứa section
    );

    // Thêm các trường cài đặt cho Messenger
    add_settings_field(
        'fcb_link_a',
        'ID Messenger',
        'fcb_link_a_html',
        'fcb_settings',
        'fcb_settings_section'
    );

    // Thêm các trường cài đặt cho Zalo
    add_settings_field(
        'fcb_link_b',
        'ID Zalo',
        'fcb_link_b_html',
        'fcb_settings',
        'fcb_settings_section'
    );

    // Thêm các trường cài đặt cho Điện thoại
    add_settings_field(
        'fcb_link_c',
        'Số Điện thoại',
        'fcb_link_c_html',
        'fcb_settings',
        'fcb_settings_section'
    );

    // Thêm các trường cài đặt cho Vị trí
    add_settings_field(
        'fcb_link_d',
        'Vị trí',
        'fcb_link_d_html',
        'fcb_settings',
        'fcb_settings_section'
    );
}
add_action('admin_init', 'fcb_register_settings');

// HTML cho trường nhập ID Messenger
function fcb_link_a_html() {
    $options = get_option('fcb_settings');
    ?>
    <input type="text" name="fcb_settings[fcb_link_a]" value="<?php echo isset($options['fcb_link_a']) ? esc_attr($options['fcb_link_a']) : ''; ?>" placeholder="ID Facebook">
    <?php
}

// HTML cho trường nhập ID Zalo
function fcb_link_b_html() {
    $options = get_option('fcb_settings');
    ?>
    <input type="text" name="fcb_settings[fcb_link_b]" value="<?php echo isset($options['fcb_link_b']) ? esc_attr($options['fcb_link_b']) : ''; ?>" placeholder="ID Zalo">
    <?php
}

// HTML cho trường nhập số điện thoại
function fcb_link_c_html() {
    $options = get_option('fcb_settings');
    ?>
    <input type="text" name="fcb_settings[fcb_link_c]" value="<?php echo isset($options['fcb_link_c']) ? esc_attr($options['fcb_link_c']) : ''; ?>" placeholder="Số điện thoại">
    <?php
}

// HTML cho trường nhập vị trí
function fcb_link_d_html() {
    $options = get_option('fcb_settings');
    ?>
    <input type="text" name="fcb_settings[fcb_link_d]" value="<?php echo isset($options['fcb_link_d']) ? esc_attr($options['fcb_link_d']) : ''; ?>" placeholder="Vị trí">
    <?php
}

// Hàm để hiển thị các liên kết trên frontend
function fcb_display_buttons() {
    $options = get_option('fcb_settings');
    
    // Hiển thị nút Messenger nếu có ID
    if (!empty($options['fcb_link_a'])) {
        echo '<a href="https://m.me/' . esc_attr($options['fcb_link_a']) . '" target="_blank" class="contact-button">Nhắn tin trên Messenger</a>';
    }
    
    // Hiển thị nút Zalo nếu có ID
    if (!empty($options['fcb_link_b'])) {
        echo '<a href="https://api.zalo.me/v2.0/oa/message?access_token=' . esc_attr($options['fcb_link_b']) . '" target="_blank" class="contact-button">Nhắn tin trên Zalo</a>';
    }
    
    // Hiển thị nút Điện thoại nếu có số điện thoại
    if (!empty($options['fcb_link_c'])) {
        echo '<a href="tel:' . esc_attr($options['fcb_link_c']) . '" class="contact-button">Gọi điện thoại</a>';
    }
    
    // Hiển thị nút Vị trí nếu có địa chỉ
    if (!empty($options['fcb_link_d'])) {
        echo '<a href="https://www.google.com/maps/search/?api=1&query=' . urlencode($options['fcb_link_d']) . '" target="_blank" class="contact-button">Xem vị trí trên bản đồ</a>';
    }
}
