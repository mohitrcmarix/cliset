<?php
/**
 * The template for displaying comments
 */

if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h3 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            if ( '1' === $comment_count ) {
                printf( esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'clitheme' ), get_the_title() );
            } else {
                printf( esc_html__( '%1$s thoughts on &ldquo;%2$s&rdquo;', 'clitheme' ), $comment_count, get_the_title() );
            }
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'      => 'ol',
                'short_ping' => true,
            ) );
            ?>
        </ol>

        <?php
        the_comments_navigation();

        if ( ! comments_open() ) :
        ?>
            <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'clitheme' ); ?></p>
        <?php
        endif;

    endif;

    comment_form();
    ?>

</div><!-- #comments -->