<?php get_header(); ?>
<div class="container" style="padding: 50px 0;">
    <h1><?php the_title(); ?></h1>
    <div class="generic-content">
        <?php 
        if(have_posts()): while(have_posts()): the_post();
            the_content();
        endwhile; endif; 
        ?>
    </div>
</div>
<?php get_footer(); ?>