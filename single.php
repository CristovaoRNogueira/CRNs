<?php get_header(); ?>

<div class="site-content-blog single-post-view">
    <div class="container container-flex">

        <main class="blog-main">
            <?php while (have_posts()):
                the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>

                    <header class="single-header" style="margin-bottom: 25px;">
                        <h1
                            style="font-size: 2.2rem; color: #333; margin: 0 0 15px 0; line-height: 1.25; font-weight: 700;">
                            <?php the_title(); ?>
                        </h1>

                        <div class="blog-meta"
                            style="display: flex; gap: 15px; color: #666; font-size: 0.95rem; align-items: center; flex-wrap: wrap; padding-bottom: 20px; border-bottom: 1px solid #eaeaea;">
                            <span style="display:flex; align-items:center; gap:5px;">
                                <span class="dashicons dashicons-calendar-alt"></span> <?php echo get_the_date(); ?>
                            </span>
                            <span style="display:flex; align-items:center; gap:5px;" class="meta-cat">
                                <span class="dashicons dashicons-category"></span> <?php the_category(', '); ?>
                            </span>
                            <span style="display:flex; align-items:center; gap:5px;">
                                <span class="dashicons dashicons-admin-users"></span> Por <?php the_author(); ?>
                            </span>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="single-thumb">
                            <?php the_post_thumbnail('large', array('loading' => 'eager', 'fetchpriority' => 'high', 'alt' => get_the_title())); ?>
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
                if (comments_open() || get_comments_number()):
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