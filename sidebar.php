<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
    <div class="widget-area">
        <?php dynamic_sidebar( 'sidebar-blog' ); ?>
    </div>
<?php else: ?>
    <div class="widget-box">
        <h3>Pesquisar</h3>
        <?php get_search_form(); ?>
    </div>
    <div class="widget-box">
        <h3>Categorias</h3>
        <ul><?php wp_list_categories('title_li='); ?></ul>
    </div>
<?php endif; ?>