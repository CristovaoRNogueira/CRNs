<?php get_header(); ?>

<section class="home-hero">
    <div class="container">
        <div class="hero-grid">
            <?php 
            // Pega os 3 últimos reviews para destaque
            $hero = new WP_Query( array(
                'post_type' => 'review',
                'posts_per_page' => 3,
                'ignore_sticky_posts' => 1
            ));

            if ( $hero->have_posts() ) : 
                $i = 0; 
                while ( $hero->have_posts() ) : $hero->the_post(); 
                    $i++;
                    $bg_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    $class = ($i == 1) ? 'hero-main' : 'hero-sub'; // 1º é Grande, outros Pequenos
            ?>
                <div class="<?php echo $class; ?>" style="background-image: url('<?php echo $bg_url; ?>');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <?php 
                        // Categoria
                        $cats = get_the_terms( get_the_ID(), 'tipo_produto' );
                        if($cats && !is_wp_error($cats)) echo '<span class="hero-cat">'.$cats[0]->name.'</span>';
                        ?>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if($i == 1): // Resumo só no grandão ?>
                            <p class="hero-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
    </div>
</section>

<section class="cat-nav-section">
    <div class="container">
        <h3 class="section-title">O que você procura?</h3>
        <div class="cat-pills">
            <?php 
            $terms = get_terms([
                'taxonomy' => 'tipo_produto',
                'hide_empty' => true,
            ]);
            
            // Ícones manuais para categorias comuns (ajuste os slugs conforme seu site)
            $icons = [
                'notebooks'   => 'dashicons-laptop',
                'smartphones' => 'dashicons-smartphone',
                'impressoras' => 'dashicons-printer',
                'acessorios'  => 'dashicons-headphones'
            ];

            foreach($terms as $term): 
                $icon = isset($icons[$term->slug]) ? $icons[$term->slug] : 'dashicons-tag';
            ?>
                <a href="<?php echo get_term_link($term); ?>" class="cat-pill">
                    <span class="dashicons <?php echo $icon; ?>"></span>
                    <?php echo $term->name; ?>
                </a>
            <?php endforeach; ?>
            
            <a href="<?php echo site_url('/ofertas'); ?>" class="cat-pill highlight">
                <span class="dashicons dashicons-tag"></span> Ver Todas Ofertas
            </a>
        </div>
    </div>
</section>

<div id="content" class="site-content home-feed">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="container">
                <header class="section-header-flex">
                    <h2 class="section-title-lg">Últimas Análises & Ofertas</h2>
                    <a href="<?php echo site_url('/ofertas'); ?>" class="btn-ver-mais">Ver tudo &rarr;</a>
                </header>

                <div class="offers-grid">
                    <?php 
                    // Query Principal (paginação funciona aqui)
                    if( have_posts() ): while( have_posts() ) : the_post(); 
                        
                        // Meta dados
                        $price = get_post_meta( get_the_ID(), '_crns_price', true );
                        $old_price = get_post_meta( get_the_ID(), '_crns_old_price', true );
                        $link = get_post_meta( get_the_ID(), '_crns_affiliate_link', true );
                        
                        // Specs para o Card
                        $all_meta = get_post_meta(get_the_ID());
                        $specs_html = ''; $c=0;
                        $priority = ['_crns_cpu', '_crns_ram', '_crns_storage', '_crns_screen', '_crns_print_tech', '_crns_camera_main'];
                        foreach($priority as $k) {
                            if($c>=2) break;
                            if(isset($all_meta[$k][0]) && $all_meta[$k][0]) {
                                $specs_html .= '<span>'.esc_html($all_meta[$k][0]).'</span> • ';
                                $c++;
                            }
                        }
                        
                        // Desconto
                        $discount_html = '';
                        if($old_price > $price && $old_price > 0) {
                            $porc = round((($old_price - $price) / $old_price) * 100);
                            $discount_html = '<span class="discount-pill">-' . $porc . '%</span>';
                        }
                    ?>
                        <div class="offer-card-v2">
                            <div class="card-img">
                                <?php echo $discount_html; ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="card-specs-mini"><?php echo $specs_html ? rtrim($specs_html, ' • ') : 'Ver detalhes'; ?></div>
                                <div class="card-price-block">
                                    <?php if($old_price > $price): ?>
                                        <small style="text-decoration:line-through; color:#ccc; font-size:0.8rem">R$ <?php echo $old_price; ?></small>
                                    <?php endif; ?>
                                    <span class="card-price">R$ <?php echo $price ? $price : 'Confira'; ?></span>
                                </div>
                            </div>
                            <div class="card-action">
                                <a href="<?php the_permalink(); ?>" class="btn-buy-v2 btn-outline">Ver Análise</a>
                            </div>
                        </div>

                    <?php endwhile; endif; ?>
                </div>

                <div class="crns-pagination"><?php the_posts_pagination(); ?></div>
            </div>
        </main>
    </div>
</div>
<?php get_footer(); ?>