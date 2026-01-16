<?php get_header(); ?>

<div class="site-content-blog single-post-view">
    <div class="container container-flex">
        
        <main class="blog-main">
            <?php while ( have_posts() ) : the_post(); ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>
                    
                    <header class="single-header">
                        <div class="blog-meta">
                            <span class="date"><?php echo get_the_date(); ?></span>
                            <span class="cat"><?php the_category(', '); ?></span>
                        </div>
                        <h1><?php the_title(); ?></h1>
                    </header>

                    <?php if(has_post_thumbnail()): ?>
                        <div class="single-thumb">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <footer class="single-footer">
                        <div class="tags-list"><?php the_tags('Tags: ', ', ', ''); ?></div>
                    </footer>

                </article>

                <?php 
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>

            <?php endwhile; ?>
        </main>

        <aside class="blog-sidebar">
            <?php get_sidebar(); ?>
        </aside>

    </div>
</div>

<?php get_footer(); ?>