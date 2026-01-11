<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
    <div id="page" class="site">
        <header id="masthead" class="site-header">
            <div class="header-main">
                <div class="container">
                    <div class="header-row">
                        <div class="site-branding">
                            <?php if( has_custom_logo() ) { the_custom_logo(); } else { ?>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-logo"><?php bloginfo( 'name' ); ?><span>.tech</span></a>
                            <?php } ?>
                        </div>

                        <div class="header-search">
                            <form role="search" method="get" class="search-form-portal" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <input type="search" class="search-field" placeholder="Busque por notebook, celular..." value="<?php echo get_search_query(); ?>" name="s" />
                                <button type="submit" class="search-submit">🔍</button>
                            </form>
                        </div>

                        <div class="header-actions">
                            <a href="<?php echo home_url('/ofertas'); ?>" class="btn-header-cta">🔥 Ofertas</a>
                        </div>
                    </div>
                </div>
            </div>

            <nav id="site-navigation" class="main-navigation">
                <div class="container">
                    <?php wp_nav_menu( array( 'theme_location' => 'wp_devs_main_menu', 'depth' => 3 ) ); ?>
                </div>
            </nav>
        </header>