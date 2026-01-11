<?php /* Template Name: Página de Ofertas */ get_header(); ?>
<div class="content-area offers-page">
    <div class="container">
        <header style="text-align:center; padding:40px 0;"><h1>🔥 Ofertas do Dia</h1></header>
        <div class="offers-grid">
            <?php 
            $offers = new WP_Query( array( 'post_type' => 'review', 'meta_key' => '_crns_price', 'posts_per_page' => 30 ) );
            if( $offers->have_posts() ): while( $offers->have_posts() ) : $offers->the_post();
                $price = get_post_meta( get_the_ID(), '_crns_price', true );
                $old = get_post_meta( get_the_ID(), '_crns_old_price', true );
                $disc = ($old > $price) ? round((($old - $price)/$old)*100) . '% OFF' : '';
            ?>
                <div class="offer-card">
                    <?php if($disc) echo "<span class='discount-badge'>$disc</span>"; ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                    <h3><?php the_title(); ?></h3>
                    <div class="price-box">
                        <?php if($old) echo "<s class='old'>R$ $old</s>"; ?>
                        <strong class='curr'>R$ <?php echo $price; ?></strong>
                    </div>
                    <a href="<?php echo get_post_meta(get_the_ID(), '_crns_affiliate_link', true); ?>" class="btn-offer">COMPRAR</a>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>