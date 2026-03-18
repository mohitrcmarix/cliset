<?php
/**
 * The template for displaying search results pages
 */

get_header();
?>

<main class="site-main">
    <div class="content-container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                printf( esc_html__( 'Search Results for: %s', 'clitheme' ), '<span>' . get_search_query() . '</span>' );
                ?>
            </h1>
        </header>

        <?php if ( have_posts() ) : ?>

            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-entry' ); ?>>
                    <header class="entry-header">
                        <h2 class="entry-title">
                            <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <div class="entry-meta">
                            <span class="posted-on"><?php echo esc_html( get_the_date() ); ?></span>
                            <span class="byline"><?php esc_html_e( 'by', 'clitheme' ); ?> <?php the_author_posts_link(); ?></span>
                        </div>
                    </header>

                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>

                    <footer class="entry-footer">
                        <a class="read-more" href="<?php echo esc_url( get_permalink() ); ?>">
                            <?php esc_html_e( 'Read more', 'clitheme' ); ?>
                        </a>
                    </footer>
                </article>
            <?php endwhile; ?>

            <nav class="pagination">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html__( 'Previous', 'clitheme' ),
                    'next_text' => esc_html__( 'Next', 'clitheme' ),
                ) );
                ?>
            </nav>

        <?php else : ?>

            <section class="no-results">
                <h2><?php esc_html_e( 'Nothing Found', 'clitheme' ); ?></h2>
                <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'clitheme' ); ?></p>
                <?php get_search_form(); ?>
            </section>

        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
