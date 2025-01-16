<?php
/**
 * Tài khoản người dùng với icon chỉnh sửa, cập nhật tên, và thông báo tùy chỉnh căn giữa.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.16.0
 */

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$updated_name = '';

// Xử lý cập nhật tên hiển thị
if ( isset( $_POST['update_display_name'] ) ) {
    // Xác minh nonce để bảo mật
    if ( wp_verify_nonce( $_POST['account_user_nonce'], 'update_display_name_nonce' ) ) {
        $new_display_name = sanitize_text_field( $_POST['new_display_name'] );
        $current_display_name = $current_user->display_name;

        // Kiểm tra xem tên mới có khác tên hiện tại không
        if ( $new_display_name !== $current_display_name ) {
            // Cập nhật tên hiển thị của người dùng
            wp_update_user( array( 'ID' => $user_id, 'display_name' => $new_display_name ) );
            $updated_name = $new_display_name; // Gán tên mới để xử lý bằng JavaScript
        }
    }
}

// Lấy avatar của người dùng (tùy chỉnh hoặc mặc định)
$user_avatar = get_user_meta( $user_id, 'user_avatar', true );

// Thêm timestamp vào URL để tránh lưu cache
$avatar_url_with_timestamp = ! empty( $user_avatar ) ? add_query_arg( 't', time(), esc_url( $user_avatar ) ) : '';

?>
<div class="account-user circle" style="position: relative;">
    <!-- Hộp chứa với nền màu xám nhạt, viền, và độ rộng cố định -->
    <div class="account-user-info" style="background-color: #f1f1f1; border: 1px solid #DDDDDD; padding: 20px; border-radius: 10px; width: 100%; max-width: 600px; margin: 0 auto; display: flex; align-items: flex-start;">
        <!-- Avatar của người dùng không có viền -->
        <span class="image mr-half inline-block" style="border-radius: 50%; overflow: hidden; width: 70px; height: 70px; flex-shrink: 0; margin-right: 15px;">
            <?php
                // Hiển thị avatar tùy chỉnh với timestamp nếu có, ngược lại hiển thị avatar mặc định
                if ( $user_avatar ) {
                    echo '<img src="' . $avatar_url_with_timestamp . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">';
                } else {
                    echo get_avatar( $user_id, 70 );
                }
            ?>
        </span>

        <!-- Tên người dùng và icon chỉnh sửa -->
        <span class="user-name inline-block" style="word-wrap: break-word; word-break: break-word; max-width: 300px; margin-top: 22px;">
            <span id="display_name_text">
                <?php echo esc_html( $current_user->display_name ); ?>
            </span>
            <!-- Icon chỉnh sửa kế bên tên người dùng -->
            <a href="#" class="edit-name-icon" style="margin-left: 3px; display: inline-flex; align-items: center;">
                <i class="fa fa-pencil" aria-hidden="true"></i> <!-- Icon Font Awesome -->
                <span style="margin-left: 2px;">Sửa</span> <!-- Văn bản "Sửa" kế bên icon -->
            </a>
        </span>
    </div>

    <!-- Form chỉnh sửa tên, mặc định bị ẩn -->
    <form method="post" class="edit-name-form" style="display: none; margin-top: 10px;">
        <p>
            <label for="new_display_name">Tên mới</label>
            <input type="text" name="new_display_name" id="new_display_name" value="<?php echo esc_attr( $current_user->display_name ); ?>" />
        </p>
        <p>
            <label for="user_email">Email</label>
            <input type="text" name="user_email" id="user_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" readonly style="opacity: 0.6;" />
        </p>
        <?php wp_nonce_field( 'update_display_name_nonce', 'account_user_nonce' ); ?>
        <p>
            <!-- Nút "Lưu thông tin" với góc bo tròn -->
            <input type="submit" name="update_display_name" value="Lưu thông tin" style="border-radius: 5px; padding: 5px 16px; background-color: #1e73be; color: white; border: none; cursor: pointer;" />
        </p>
    </form>

    <?php do_action('flatsome_after_account_user'); ?>
</div>

<!-- Thêm Font Awesome cho icon chỉnh sửa -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Thêm jQuery nếu website của bạn chưa có -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Khi click vào icon chỉnh sửa
    $('.edit-name-icon').on('click', function(e) {
        e.preventDefault();
        // Hiển thị/Ẩn form chỉnh sửa
        $(this).closest('.account-user').find('.edit-name-form').slideToggle();
    });

    // Nếu tên mới đã được cập nhật qua PHP
    <?php if ( $updated_name ) : ?>
        // Cập nhật tên hiển thị trên trang
        $('#display_name_text').text('<?php echo esc_js( $updated_name ); ?>');
        // Cập nhật tên mới trong trường input
        $('#new_display_name').val('<?php echo esc_js( $updated_name ); ?>');
    <?php endif; ?>
});
</script>