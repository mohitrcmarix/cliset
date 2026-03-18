<?php
/**
 * Template for displaying search forms
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'clitheme' ); ?></span>
        <input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search &hellip;', 'clitheme' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <input type="submit" class="search-submit" value="<?php esc_attr_e( 'Search', 'clitheme' ); ?>" />
</form>