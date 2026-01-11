<?php get_header(); ?>
<div id="content" class="site-content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="container">
                <header class="page-header"><h1 class="page-title">Últimas Análises</h1></header>
                <div class="blog-grid">
                    <?php if( have_posts() ): while( have_posts() ) : the_post(); ?>
                            <article <?php post_class('grid-item'); ?>>
                                <div class="grid-thumb">
                                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large'); ?></a>
                                    <?php $rate = get_post_meta( get_the_ID(), '_crns_rating', true ); 
                                    if($rate) echo "<span class='grid-rating'>$rate</span>"; ?>
                                </div>
                                <div class="grid-content">
                                    <h3 class="grid-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p><?php echo wp_trim_words( get_the_excerpt(), 12 ); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="grid-link">Ler Review</a>
                                </div>
                            </article>
                    <?php endwhile; endif; ?>
                </div>
                <div class="crns-pagination"><?php the_posts_pagination(); ?></div>
            </div>
        </main>
    </div>
</div>
<?php get_footer(); ?>