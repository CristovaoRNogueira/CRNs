<?php get_header(); ?>

<section class="hero-split">
    <div class="container">
        <div class="split-wrapper">
            
            <div class="split-content">
                <span class="badge-hero">Bem-vindo ao <?php bloginfo('name'); ?></span>
                
                <h1>Descubra qual é o <br><span class="highlight-text">Melhor Eletrônico</span> para você</h1>
                
                <p class="hero-description">
                    Nós testamos e comparamos os principais lançamentos de <strong>Notebooks</strong>, <strong>Smartphones</strong> e <strong>Periféricos</strong>. 
                    Não gaste seu dinheiro errado: confira nossas análises técnicas, guias de compra e encontre o menor preço do dia.
                </p>

                <div class="hero-buttons">
                    <a href="<?php echo site_url('/ofertas'); ?>" class="btn-hero-primary">
                        <span class="dashicons dashicons-tag"></span> Ver Melhores Ofertas
                    </a>
                    
                    <a href="https://chat.whatsapp.com/L1gN8KvrxVH63AY591Rrbs" target="_blank" class="btn-hero-whatsapp">
                        <span class="dashicons dashicons-whatsapp"></span> Participe dos Grupos
                    </a>
                </div>
                
                <p class="hero-note">Receba descontos reais em tempo real no seu celular.</p>
            </div>

            <div class="split-visuals">
                <div class="mini-grid">
                    <?php 
                    // Pega os 3 últimos posts
                    $hero = new WP_Query( array( 'post_type' => 'review', 'posts_per_page' => 3, 'ignore_sticky_posts' => 1 ));
                    if ( $hero->have_posts() ) : $i = 0; while ( $hero->have_posts() ) : $hero->the_post(); $i++;
                        $bg_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                        // O primeiro é grande (Main), os outros 2 são pequenos (Sub)
                        $class = ($i == 1) ? 'visual-main' : 'visual-sub'; 
                    ?>
                        <div class="<?php echo $class; ?>" style="background-image: url('<?php echo $bg_url; ?>');">
                            <a href="<?php the_permalink(); ?>" class="visual-link">
                                <div class="visual-overlay">
                                    <?php $cats = get_the_terms( get_the_ID(), 'tipo_produto' ); if($cats) echo '<span class="visual-cat">'.$cats[0]->name.'</span>'; ?>
                                    <h3><?php echo wp_trim_words( get_the_title(), 8 ); ?></h3>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; wp_reset_postdata(); endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="profiles-section" style="margin-bottom:50px;">
    <div class="container">
        
        <h4 class="section-subtitle">Notebooks</h4>
        <div class="profile-grid">
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=de-entrada'); ?>" class="profile-card">
                <span class="dashicons dashicons-book"></span><span>Estudos / Básico</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=intermediario'); ?>" class="profile-card">
                <span class="dashicons dashicons-laptop"></span><span>Trabalho</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=gamers'); ?>" class="profile-card highlight">
                <span class="dashicons dashicons-games"></span><span>Gamers</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=premium'); ?>" class="profile-card">
                <span class="dashicons dashicons-star-filled"></span><span>Premium</span>
            </a>
        </div>

        <h4 class="section-subtitle">Smartphones</h4>
        <div class="profile-grid">
            <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=custo-beneficio'); ?>" class="profile-card">
                <span class="dashicons dashicons-money"></span><span>Custo-Benefício</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=top-de-linha'); ?>" class="profile-card">
                <span class="dashicons dashicons-awards"></span><span>Top de Linha</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=melhores-cameras'); ?>" class="profile-card">
                <span class="dashicons dashicons-camera"></span><span>Câmeras</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=gamers'); ?>" class="profile-card highlight">
                <span class="dashicons dashicons-games"></span><span>Gamers</span>
            </a>
        </div>
        
        <h4 class="section-subtitle">Impressoras</h4>
        <div class="profile-grid">
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=multifuncional'); ?>" class="profile-card">
                <span class="dashicons dashicons-printer"></span><span>Multifuncional</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=tanque-de-tinta'); ?>" class="profile-card">
                <span class="dashicons dashicons-admin-tint"></span><span>Tanque de Tinta</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=wifi'); ?>" class="profile-card">
                <span class="dashicons dashicons-wifi"></span><span>Wi-Fi</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=escritorio'); ?>" class="profile-card highlight">
                <span class="dashicons dashicons-building"></span><span>Escritório</span>
            </a>
        </div>
    </div>
</section>

<div id="content" class="site-content home-feed">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="container">
                <header class="section-header-flex">
                    <h2 class="section-title-lg">Últimas Análises</h2>
                    <a href="<?php echo site_url('/ofertas'); ?>" class="btn-ver-mais">Ver tudo &rarr;</a>
                </header>

                <div class="offers-grid">
                    <?php 
                    // CRIA UMA NOVA CONSULTA ESPECÍFICA PARA REVIEWS
                    $args_home = array(
                        'post_type'      => 'review', // Garante que pega os Reviews (Produtos)
                        'posts_per_page' => 8,        // Quantidade de itens
                        'post_status'    => 'publish',
                        'orderby'        => 'date',
                        'order'          => 'DESC'
                    );

                    $home_query = new WP_Query( $args_home );

                    if( $home_query->have_posts() ): 
                        while( $home_query->have_posts() ) : $home_query->the_post(); 
                        
                        // Captura dados
                        $price = get_post_meta( get_the_ID(), '_crns_price', true );
                        $old_price = get_post_meta( get_the_ID(), '_crns_old_price', true );
                        $link = get_post_meta( get_the_ID(), '_crns_affiliate_link', true );
                        
                        // Specs
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

                        // Lógica de Desconto
                        $discount_html = '';
                        if($old_price > $price && $old_price > 0) {
                            $porc = round((($old_price - $price) / $old_price) * 100);
                            $discount_html = '<span class="discount-pill">-' . $porc . '%</span>';
                        }
                    ?>
                        <div class="offer-card-v2">
                            <div class="card-img">
                                <?php echo $discount_html; ?>
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
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

                    <?php endwhile; 
                    // IMPORTANTE: Reseta os dados para não bugar o resto da página
                    wp_reset_postdata(); 
                    else: ?>
                        <p>Nenhuma análise encontrada no momento.</p>
                    <?php endif; ?>
                </div>

                <div class="crns-pagination">
                    <a href="<?php echo site_url('/ofertas'); ?>" class="btn-hero-primary" style="margin: 0 auto; display: table;">Ver Todas as Ofertas</a>
                </div>
            </div>
        </main>
    </div>
</div>
<?php get_footer(); ?>