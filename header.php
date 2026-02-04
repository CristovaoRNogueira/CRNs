<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <meta name="google-adsense-account" content="ca-pub-5036236896273491">

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5036236896273491"
        crossorigin="anonymous"></script>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div id="page" class="site">

        <header id="masthead" class="site-header">

            <div class="header-main-bar">
                <div class="container">
                    <div class="header-wrapper">

                        <button id="mobile-menu-trigger" class="mobile-toggle">
                            <span class="dashicons dashicons-menu-alt3"></span>
                        </button>

                        <div class="site-branding">
                            <?php if (has_custom_logo()): ?>
                                <?php the_custom_logo(); ?>
                            <?php else: ?>
                                <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"
                                        rel="home"><?php bloginfo('name'); ?></a></p>
                            <?php endif; ?>
                        </div>

                        <nav class="desktop-nav-center">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'desktop_center',
                                'container' => false,
                                'menu_class' => 'icon-menu-list',
                                'fallback_cb' => false
                            ));
                            ?>
                        </nav>

                        <div class="header-actions">
                            <button id="search-toggle-btn" class="search-toggle-btn">
                                <span class="dashicons dashicons-search"></span>
                            </button>

                            <div id="search-expanded-bar" class="search-expanded-bar">
                                <form role="search" method="get" class="search-form-expanded"
                                    action="<?php echo esc_url(home_url('/')); ?>">
                                    <input type="search" class="search-input-field" placeholder="Pesquisar..."
                                        value="<?php echo get_search_query(); ?>" name="s" />
                                    <button type="submit" class="search-submit-btn"><span
                                            class="dashicons dashicons-search"></span></button>
                                    <button type="button" id="close-search-btn" class="close-search">&times;</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mobile-cat-strip">
                <div class="container">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'desktop_center',
                        'container' => false,
                        'menu_class' => 'mobile-icon-scroll',
                        'fallback_cb' => false
                    ));
                    ?>
                </div>
            </div>

        </header>

        <div id="mobile-drawer" class="mobile-drawer">
            <div class="drawer-header">
                <span class="drawer-title">Menu</span>
                <button id="close-drawer" class="close-btn">&times;</button>
            </div>
            <div class="drawer-content">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'drawer_menu',
                    'container' => false,
                    'menu_class' => 'drawer-menu-list'
                ));
                ?>
            </div>
        </div>
        <div id="drawer-overlay" class="drawer-overlay"></div>