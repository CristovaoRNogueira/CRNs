<?php get_header(); ?>
<div id="content" class="site-content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="container">
                <div class="error-404">
                    <header>
                        <h1><?php esc_html_e( 'Page not found', 'crns' ); ?></h1>
                        <p><?php esc_html_e( 'Unfortunately, the page you tried to reach does not exist on this site.', 'crns' ) ?></p>
                    </header>

                    <div class="error">
                        <p><?php esc_html_e( 'How about doing a search?', 'crns' ) ?></p>
                        <?php get_search_form(); ?>
                        <?php 
                        the_widget( 
                            'WP_Widget_Recent_Posts',
                            array(
                                'title' => esc_html__( 'Latest Posts', 'crns' ),
                                'number'    => 3
                            ) 
                        ); 
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php get_footer(); ?>