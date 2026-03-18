<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header();
?>

<main class="site-main">
    <div class="content-container">
        <section class="error-404">
            <h1 class="page-title"><?php esc_html_e( 'Oops! That page can’t be found.', 'clitheme' ); ?></h1>
            <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'clitheme' ); ?></p>

            <div class="error-404-actions">
                <?php get_search_form(); ?>

                <div class="error-404-links">
                    <h3><?php esc_html_e( 'Most Used Categories', 'clitheme' ); ?></h3>
                    <ul>
                        <?php
                        wp_list_categories( array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'show_count' => 1,
                            'title_li'   => '',
                            'number'     => 10,
                        ) );
                        ?>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();
