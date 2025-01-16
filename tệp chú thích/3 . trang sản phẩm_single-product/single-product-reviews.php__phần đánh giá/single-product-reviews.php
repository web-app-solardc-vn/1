<?php
/**
 * Display single product reviews (comments)
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.3.0
 */

defined('ABSPATH') || exit;

global $product;

if (!comments_open()) {
    return;
}

$tab_style = get_theme_mod('product_display');
$review_ratings_enabled = wc_review_ratings_enabled();
?>

<div id="reviews" class="woocommerce-Reviews">
    <div class="rating-overview-container">
        <div class="rating-average-section">
            <div class="rating-summary">
                <h4 class="average-rating"><?php 
                    $average = $product->get_average_rating();
                    echo number_format($average, 1) . ' / 5';
                ?></h4>
                <p class="rating-count">
                    <?php printf(_n('%s Review', '%s Reviews', $product->get_review_count(), 'woocommerce'), 
                        number_format_i18n($product->get_review_count())); ?>
                </p>
            </div>
        </div>

        <div class="rating-distribution-section">
            <div class="rating-bars">
                <?php
                $rating_counts = $product->get_rating_counts();
                $total_ratings = array_sum($rating_counts);
                for ($i = 5; $i >= 1; $i--) {
                    $count = isset($rating_counts[$i]) ? $rating_counts[$i] : 0;
                    $percentage = $total_ratings ? ($count / $total_ratings) * 100 : 0;
                    ?>
                    <div class="rating-bar-row">
                        <span class="rating-label"><?php echo $i; ?> sao</span>
                        <div class="rating-bar">
                            <div class="rating-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <span class="rating-percentage"><?php echo round($percentage); ?>%</span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="reviews-container row">
        <?php if (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())) : ?>
            <div id="review_form_wrapper" class="large-<?php echo (get_comment_pages_count() == 0 || $tab_style == 'sections' || $tab_style == 'tabs_vertical') ? '12' : '5'; ?> col">
                <div id="review_form" class="col-inner">
                    <div class="review-form-inner">
                    <?php
                    $commenter    = wp_get_current_commenter();
                    $comment_form = array(
                        'title_reply'          => have_comments() ? esc_html__( 'Add a review', 'woocommerce' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
                        'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
                        'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
                        'title_reply_after'    => '</h3>',
                        'comment_notes_before' => '',
                        'comment_notes_after'  => '',
                        'label_submit'         => esc_html__( 'Submit', 'woocommerce' ),
                        'logged_in_as'         => '',
                        'comment_field'        => '',
                    );

                    $name_email_required = (bool) get_option( 'require_name_email', 1 );
                    $fields              = array(
                        'author' => array(
                            'label'    => __( 'Name', 'woocommerce' ),
                            'type'     => 'text',
                            'value'    => $commenter['comment_author'],
                            'required' => $name_email_required,
                        ),
                        'email'  => array(
                            'label'    => __( 'Email', 'woocommerce' ),
                            'type'     => 'email',
                            'value'    => $commenter['comment_author_email'],
                            'required' => $name_email_required,
                        ),
                    );

                    $comment_form['fields'] = array();

                    foreach ( $fields as $key => $field ) {
                        $field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
                        $field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

                        if ( $field['required'] ) {
                            $field_html .= '&nbsp;<span class="required">*</span>';
                        }

                        $field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

                        $comment_form['fields'][ $key ] = $field_html;
                    }

                    $account_page_url = wc_get_page_permalink( 'myaccount' );
                    if ( $account_page_url ) {
                        $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
                    }

                    if ( $review_ratings_enabled && wc_customer_bought_product('', get_current_user_id(), $product->get_id()) ) {
                        $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
                            <option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
                            <option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
                            <option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
                            <option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
                            <option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
                            <option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
                        </select></div>';
                    }

                    $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                    comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
                    ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div id="comments" class="col large-<?php echo (get_comment_pages_count() == 0 || $tab_style == 'sections' || $tab_style == 'tabs_vertical') ? '12' : '7'; ?>">
            <h3 class="woocommerce-Reviews-title normal">
                <?php
                $count = $product->get_review_count();
                if ( $count && $review_ratings_enabled ) {
                    $reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
                    echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product );
                } else {
                    esc_html_e( 'Reviews', 'woocommerce' );
                }
                ?>
            </h3>

            <?php if ( have_comments() ) : ?>
                <ol class="commentlist">
                    <?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
                </ol>

                <?php
                if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
                    echo '<nav class="woocommerce-pagination">';
                    $pagination = paginate_comments_links(
                        apply_filters(
                            'woocommerce_comment_pagination_args',
                            array(
                                'prev_text' => '&larr;',
                                'next_text' => '&rarr;',
                                'type'      => 'list',
                                'echo'      => false,
                            )
                        )
                    );
                    $pagination = str_replace( 'page-numbers', 'page-number', $pagination );
                    $pagination = str_replace( "<ul class='page-number'>", '<ul class="page-numbers nav-pagination links text-center">', $pagination );
                    $pagination = str_replace( '<a class="next page-number', '<a aria-label="' . esc_attr__( 'Next', 'flatsome' ) . '" class="next page-number', $pagination );
                    $pagination = str_replace( '<a class="prev page-number', '<a aria-label="' . esc_attr__( 'Previous', 'flatsome' ) . '" class="prev page-number', $pagination );
                    echo $pagination;
                    echo '</nav>';
                endif;
                ?>
            <?php else : ?>
                <p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'woocommerce' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.rating-overview-container {
    display: flex;
    width: 100%;
    min-height: 200px;
    margin-bottom: 30px;
    border-bottom: 1px solid #eee;
}

.rating-average-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background-color: #f9f9f9;
    border-right: 1px solid #eee;
}

.rating-summary {
    text-align: center;
}

.average-rating {
    font-size: 48px;
    font-weight: bold;
    margin: 0;
    line-height: 1;
    color: #666666;
}

.rating-count {
    font-size: 14px;
    color: #666;
    margin: 5px 0 0;
}

.rating-distribution-section {
    flex: 1;
    padding: 20px;
    display: flex;
    align-items: center;
    background-color: #fff;
}

.rating-bars {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    padding: 10px;
}

.rating-bar-row {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.rating-label {
    width: 50px;
    font-size: 14px;
    color: #666;
    text-align: left;
}

.rating-bar {
    flex-grow: 1;
    height: 16px;
    background-color: #f0f0f0;
    margin: 0 10px;
    border-radius: 3px;
    overflow: hidden;
}

.rating-fill {
    height: 100%;
    background-color: #ffd700;
    transition: width 0.3s ease;
}

.rating-percentage {
    width: 45px;
    font-size: 14px;
    color: #666;
    text-align: right;
}

@media screen and (max-width: 768px) {
    .rating-overview-container {
        flex-direction: column;
    }

    .rating-average-section {
        border-right: none;
        border-bottom: 1px solid #eee;
    }

    .rating-distribution-section {
        padding: 20px 10px;
    }

    .rating-bars {
        padding: 0;
    }
}

@media screen and (max-width: 480px) {
    .average-rating {
        font-size: 28px;
    }

    .rating-bar-row {
        margin-bottom: 8px;
    }

    .rating-label {
        width: 40px;
        font-size: 12px;
    }

    .rating-percentage {
        width: 35px;
        font-size: 12px;
    }
}
</style>