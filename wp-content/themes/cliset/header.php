<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="header-container">
        <!-- Branding -->
        <div class="site-branding">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
                <?php bloginfo( 'name' ); ?>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="main-navigation">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false,
            ) );
            ?>
        </nav>

        <!-- Call to Action Buttons -->
         <?php 
         if(!is_user_logged_in()){
         ?>
        <div class="header-cta">
            <a href="<?php echo esc_url(home_url('/cart/')); ?>" class="btn btn-quote">Cart</a>
            <a href="<?php echo esc_url(home_url('/sign-in/')); ?>" class="btn btn-trial">Sign-in</a>
        </div>
        <?php 
        } else
        { ?>
        <div class="header-cta">
            <a href="<?php echo esc_url(home_url('/cart/')); ?>" class="btn btn-quote">Cart</a>
            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="btn btn-trial">Logout</a>
        </div>
        <?php } ?>  
    </div>
</header>
