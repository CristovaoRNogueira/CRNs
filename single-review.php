<?php get_header(); ?>
<div id="primary" class="content-area-review">
    <main id="main" class="site-main">
        <?php while( have_posts() ): the_post(); 
            $meta = get_post_meta( get_the_ID() );
            $price = isset($meta['_crns_price'][0]) ? $meta['_crns_price'][0] : '';
            $link = isset($meta['_crns_affiliate_link'][0]) ? $meta['_crns_affiliate_link'][0] : '';
            $rating = isset($meta['_crns_rating'][0]) ? $meta['_crns_rating'][0] : '';
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('review-container'); ?>>
                <header class="review-header">
                    <div class="container">
                        <h1><?php the_title(); ?></h1>
                        <span class="date">Atualizado em: <?php echo get_the_modified_date('d/m/Y'); ?></span>
                    </div>
                </header>

                <div class="container review-grid">
                    <div class="review-main-card">
                        <div class="product-image-box">
                            <?php the_post_thumbnail('large'); ?>
                            <?php if($rating): ?><div class="rating-badge"><?php echo $rating; ?>/10</div><?php endif; ?>
                        </div>
                        <div class="cta-box">
                            <div class="price-tag">
                                <small>Melhor preço hoje:</small>
                                <strong>R$ <?php echo number_format((float)$price, 2, ',', '.'); ?></strong>
                            </div>
                            <?php if($link): ?>
                                <a href="<?php echo esc_url($link); ?>" target="_blank" rel="nofollow sponsored" class="btn-affiliate">VER NA LOJA</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="specs-box">
                        <h3>Ficha Técnica</h3>
                        <ul>
                            <li><strong>Processador:</strong> <?php echo $meta['_crns_cpu'][0] ?? '-'; ?></li>
                            <li><strong>RAM:</strong> <?php echo $meta['_crns_ram'][0] ?? '-'; ?></li>
                            <li><strong>GPU:</strong> <?php echo $meta['_crns_gpu'][0] ?? '-'; ?></li>
                            <li><strong>Armazenamento:</strong> <?php echo $meta['_crns_storage'][0] ?? '-'; ?></li>
                            <li><strong>Tela:</strong> <?php echo $meta['_crns_screen'][0] ?? '-'; ?></li>
                        </ul>
                    </div>
                </div>

                <div class="container review-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </main>
</div>
<?php get_footer(); ?>