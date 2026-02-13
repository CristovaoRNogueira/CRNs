<?php get_header(); ?>

<div id="content" class="site-content home-feed">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            
            <header class="archive-header-custom" style="background:#f7f7f7; padding: 40px 0; margin-bottom: 30px; border-bottom:1px solid #eaeaea;">
                <div class="container">
                    <h1 class="archive-title" style="margin:0; font-size: 2rem; color: #333; font-weight:700;">
                        <?php esc_html_e( 'Resultados para:', 'crns' ) ?> "<?php echo get_search_query(); ?>"
                    </h1>
                    
                    <div class="search-page-form" style="margin-top: 20px; max-width: 500px;">
                        <?php get_search_form(); ?>
                    </div>
                </div>
            </header>

            <div class="container">
                
                <?php if( have_posts() ): ?>
                    
                    <div class="offers-grid ml-grid-layout home-grid">
                        <?php while( have_posts() ) : the_post(); 
                            
                            $is_product = (get_post_type() === 'review');

                            // Lógica de Preço (Apenas para produtos)
                            $price_num = 0; $old_price_num = 0; $discount_html = '';
                            
                            if ( $is_product ) {
                                $price_raw = get_post_meta(get_the_ID(), '_crns_price', true);
                                $old_price_raw = get_post_meta(get_the_ID(), '_crns_old_price', true);
                                
                                $price_num = str_replace(',', '.', str_replace('.', '', $price_raw));
                                $old_price_num = str_replace(',', '.', str_replace('.', '', $old_price_raw));

                                if ($old_price_num > $price_num && $old_price_num > 0) {
                                    $porc = round((($old_price_num - $price_num) / $old_price_num) * 100);
                                    $discount_html = '<span class="discount-pill">-' . $porc . '%</span>';
                                }
                            }
                        ?>
                            
                            <div class="offer-card-v2 ml-card">
                                <a href="<?php the_permalink(); ?>" class="ml-card-link">
                                    <div class="card-img" style="position:relative;">
                                        
                                        <?php if( ! $is_product ): ?>
                                            <span style="position:absolute; top:10px; left:10px; background:#0073aa; color:#fff; padding:3px 8px; border-radius:4px; font-size:11px; z-index:2; font-weight:bold;">Artigo / Notícia</span>
                                        <?php else: ?>
                                            <?php echo $discount_html; ?>
                                        <?php endif; ?>

                                        <?php 
                                        if ( has_post_thumbnail() ) {
                                            the_post_thumbnail('large', array(
                                                'loading' => 'lazy',
                                                'alt'     => get_the_title()
                                            )); 
                                        } else {
                                            echo '<div style="background:#eee; height:100%; display:flex; align-items:center; justify-content:center; color:#999;"><span class="dashicons dashicons-format-image" style="font-size:40px; width:40px; height:40px;"></span></div>';
                                        }
                                        ?>
                                    </div>

                                    <div class="card-info">
                                        <h3 class="card-title"><?php the_title(); ?></h3>
                                        
                                        <div class="card-price-block">
                                            <?php if ($is_product && $price_num > 0): ?>
                                                <?php if ($old_price_num > $price_num): ?>
                                                    <span class="card-old-price">R$ <?php echo number_format((float)$old_price_num, 2, ',', '.'); ?></span>
                                                <?php endif; ?>
                                                
                                                <div class="ml-price-row">
                                                    <span class="card-price">R$ <?php echo number_format((float)$price_num, 2, ',', '.'); ?></span>
                                                </div>
                                            <?php elseif ( !$is_product ): ?>
                                                <span style="color:#666; font-size:13px; margin-top:10px; display:block;">
                                                    <span class="dashicons dashicons-calendar-alt" style="font-size:14px; margin-top:2px;"></span> <?php echo get_the_date(); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card-action">
                                        <span class="btn-buy-v2 btn-outline">
                                            <?php echo $is_product ? 'Ver Detalhes' : 'Ler Artigo'; ?>
                                        </span>
                                    </div>
                                </a>
                            </div>

                        <?php endwhile; ?>
                    </div>

                    <div class="crns-pagination" style="margin-top: 40px; text-align:center;">
                        <?php 
                        the_posts_pagination( array(
                            'mid_size'  => 2,
                            'prev_text' => '<span class="dashicons dashicons-arrow-left-alt2"></span> Anterior',
                            'next_text' => 'Próxima <span class="dashicons dashicons-arrow-right-alt2"></span>',
                        ) ); 
                        ?>
                    </div>

                <?php else: ?>
                    <div class="no-results" style="text-align:center; padding: 60px 20px;">
                        <span class="dashicons dashicons-search" style="font-size: 48px; color: #ccc; margin-bottom:15px;"></span>
                        <h3>Nenhum resultado encontrado</h3>
                        <p style="color:#666; font-size:1.1rem;">Não encontramos produtos ou artigos para a busca <strong>"<?php echo esc_html(get_search_query()); ?>"</strong>.</p>
                        <p style="color:#999; margin-bottom:20px;">Tente usar palavras-chave diferentes ou mais genéricas.</p>
                        
                        
                        
                        <a href="<?php echo esc_url( home_url( '/ofertas' ) ); ?>" class="btn-hero-primary" style="margin-top:30px; display:inline-block;">Ver todas as ofertas</a>
                    </div>
                <?php endif; ?>                                
                
            </div>
        </main>
    </div>
</div>

<?php get_footer(); ?>