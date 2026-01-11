<?php get_header(); ?>

<div id="primary" class="content-area-review">
    <main id="main" class="site-main">
        <?php while( have_posts() ): the_post(); 
            $price = get_post_meta( get_the_ID(), '_crns_price', true );
            $link = get_post_meta( get_the_ID(), '_crns_affiliate_link', true );
            $rating = get_post_meta( get_the_ID(), '_crns_rating', true );
            
            // Detecta categoria com fallback
            $terms = get_the_terms( get_the_ID(), 'tipo_produto' );
            $cat_slug = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->slug : 'notebooks';
            
            // Debug para você ver no código fonte se algo der errado
            echo "";
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('review-container'); ?>>
                <header class="review-header">
                    <div class="container">
                        <span class="cat-label"><?php echo isset($terms[0]) ? $terms[0]->name : 'Review'; ?></span>
                        <h1><?php the_title(); ?></h1>
                    </div>
                </header>

                <div class="container review-grid">
                    <div class="review-main-card">
                        <div class="product-image-box">
                            <?php the_post_thumbnail('large'); ?>
                            <?php if($rating): ?><div class="rating-badge"><?php echo $rating; ?></div><?php endif; ?>
                        </div>
                        <div class="cta-box">
                            <div class="price-tag"><strong>R$ <?php echo $price; ?></strong></div>
                            <?php if($link): ?><a href="<?php echo $link; ?>" class="btn-affiliate pulse">VER NA LOJA</a><?php endif; ?>
                        </div>
                    </div>

                    <div class="specs-box">
                        <h3>Ficha Técnica</h3>
                        <ul>
                            <?php 
                            // Puxa as specs corretas usando a função normalizada
                            $keys = crns_get_specs_by_category( $cat_slug );
                            $labels = crns_get_all_specs_labels();
                            
                            $found = false;
                            foreach($keys as $k) {
                                $val = get_post_meta( get_the_ID(), $k, true );
                                if($val) {
                                    $label = isset($labels[$k]) ? $labels[$k] : $k;
                                    echo "<li><strong>$label:</strong> <span>$val</span></li>";
                                    $found = true;
                                }
                            }
                            if(!$found) echo "<li>Especificações não cadastradas. Edite o produto.</li>";
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="container review-content"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </main>
</div>
<?php get_footer(); ?>