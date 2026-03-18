<footer class="site-footer">
    <div class="footer-container">
        <!-- About / Branding -->
        <div class="footer-about">
            <h3><?php bloginfo( 'name' ); ?></h3>
            <p><?php bloginfo( 'description' ); ?></p>
        </div>

        <!-- Quick Links -->
        <div class="footer-links">
            <h4>Quick Links</h4>
            <?php
            wp_nav_menu( array(
                'theme_location' => 'footer',
                'menu_class'     => 'footer-menu',
                'container'      => false,
            ) );
            ?>
        </div>

        <!-- Categories -->
        <div class="footer-categories">
            <h4>Categories</h4>
            <ul>
                <?php
                wp_list_categories( array(
                    'title_li' => '',
                    'number'   => 5,
                ) );
                ?>
            </ul>
        </div>

        <!-- Newsletter Signup -->
        <div class="footer-newsletter">
            <h4>Subscribe</h4>
            <form action="#" method="post">
                <input type="email" name="email" placeholder="Your email address">
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
