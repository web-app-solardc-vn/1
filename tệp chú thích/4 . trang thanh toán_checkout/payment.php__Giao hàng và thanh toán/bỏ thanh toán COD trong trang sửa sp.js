//___________________________________________________________________________________________
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

//_______________________________________________________________________________________