<?php get_header(); ?>

<div class="site-content-blog">
    <div class="container container-flex">
        
        <aside class="blog-sidebar">
            <?php get_sidebar(); ?>
        </aside>

        <main class="blog-main">
            
            <header class="blog-header">
                <h1 class="page-title">Blog & Notícias</h1>
                <p>Acompanhe nossas dicas, tutoriais e novidades sobre tecnologia.</p>
            </header>

            <div class="blog-list blog-grid-layout">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
                        <?php if(has_post_thumbnail()): ?>
                            <a href="<?php the_permalink(); ?>" class="blog-thumb">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </a>
                        <?php endif; ?>

                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="date"><?php echo get_the_date('d/m/Y'); ?></span>
                                </div>

                            <h2 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            
                            <div class="blog-excerpt">
                                <?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="btn-read-more">Ler Mais &rarr;</a>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>
            
            <div class="crns-pagination">
                <?php the_posts_pagination(); ?>
            </div>

            <?php else : ?>
                <p>Nenhum artigo encontrado.</p>
            <?php endif; ?>

        </main>

    </div>
</div>

<?php get_footer(); ?>