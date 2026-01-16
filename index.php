<?php get_header(); ?>

<section class="home-hero">
    <div class="container">
        <div class="hero-grid">
            <?php 
            $hero = new WP_Query( array( 'post_type' => 'review', 'posts_per_page' => 3, 'ignore_sticky_posts' => 1 ));
            if ( $hero->have_posts() ) : $i = 0; while ( $hero->have_posts() ) : $hero->the_post(); $i++;
                $bg_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                $class = ($i == 1) ? 'hero-main' : 'hero-sub'; 
            ?>
                <div class="<?php echo $class; ?>" style="background-image: url('<?php echo $bg_url; ?>');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <?php $cats = get_the_terms( get_the_ID(), 'tipo_produto' ); if($cats) echo '<span class="hero-cat">'.$cats[0]->name.'</span>'; ?>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if($i == 1): ?><p class="hero-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p><?php endif; ?>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
    </div>
</section>

<section class="profiles-section" style="margin-bottom:50px;">
    <div class="container">
        <h3 class="section-title">Encontre o ideal para você</h3>
        
        <h4 style="margin-bottom:15px; color:#666; font-size:0.9rem; text-transform:uppercase;">Notebooks</h4>
        <div class="profile-grid">
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=de-entrada'); ?>" class="profile-card">
                <span class="dashicons dashicons-book"></span>
                <span>Estudos / Básico</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=intermediario'); ?>" class="profile-card">
                <span class="dashicons dashicons-laptop"></span>
                <span>Trabalho / Intermediário</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=gamers'); ?>" class="profile-card highlight">
                <span class="dashicons dashicons-games"></span>
                <span>Gamers</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=premium'); ?>" class="profile-card">
                <span class="dashicons dashicons-star-filled"></span>
                <span>Premium / Ultrafinos</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=notebooks&classificacao=2-em-1'); ?>" class="profile-card">
                <span class="dashicons dashicons-tablet"></span>
                <span>2 em 1</span>
            </a>
        </div>

        <h4 style="margin-bottom:15px; margin-top:30px; color:#666; font-size:0.9rem; text-transform:uppercase;">Smartphones</h4>
        <div class="profile-grid">
            <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=custo-beneficio'); ?>" class="profile-card">
                <span class="dashicons dashicons-money"></span>
                <span>Custo-Benefício</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=top-de-linha'); ?>" class="profile-card">
                <span class="dashicons dashicons-awards"></span>
                <span>Top de Linha</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=melhores-cameras'); ?>" class="profile-card">
                <span class="dashicons dashicons-camera"></span>
                <span>Melhores Câmeras</span>
            </a>
             <a href="<?php echo site_url('/ofertas/?filtro_cat=smartphones&classificacao=gamers'); ?>" class="profile-card highlight">
                <span class="dashicons dashicons-games"></span>
                <span>Gamers</span>
            </a>
        </div>

        <h4 style="margin-bottom:15px; margin-top:30px; color:#666; font-size:0.9rem; text-transform:uppercase;">Impressoras</h4>
        <div class="profile-grid">
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=multifuncional'); ?>" class="profile-card">
                <span class="dashicons dashicons-printer"></span>
                <span>Multifuncional</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=colorida'); ?>" class="profile-card">
                <span class="dashicons dashicons-art"></span>
                <span>Colorida</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=preto-e-branco'); ?>" class="profile-card">
                <span class="dashicons dashicons-media-text"></span>
                <span>Preto e Branco</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=para-empresa'); ?>" class="profile-card highlight">
                <span class="dashicons dashicons-building"></span>
                <span>Para Empresa</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=para-casa-e-foto'); ?>" class="profile-card">
                <span class="dashicons dashicons-camera"></span>
                <span>Para Casa e Foto</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=wifi'); ?>" class="profile-card">
                <span class="dashicons dashicons-wifi"></span>
                <span>Wi-Fi</span>
            </a>
        </div>

    </div>
</section>

<div id="content" class="site-content home-feed">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="container">
                <header class="section-header-flex">
                    <h2 class="section-title-lg">Últimos Lançamentos</h2>
                    <a href="<?php echo site_url('/ofertas'); ?>" class="btn-ver-mais">Ver tudo &rarr;</a>
                </header>

                <div class="offers-grid">
                    <?php if( have_posts() ): while( have_posts() ) : the_post(); 
                        $price = get_post_meta( get_the_ID(), '_crns_price', true );
                        $old_price = get_post_meta( get_the_ID(), '_crns_old_price', true );
                        
                        $all_meta = get_post_meta(get_the_ID());
                        $specs_html = ''; $c=0;
                        $priority = ['_crns_cpu', '_crns_ram', '_crns_storage', '_crns_screen', '_crns_print_tech', '_crns_camera_main'];
                        foreach($priority as $k) { if($c>=2) break; if(isset($all_meta[$k][0]) && $all_meta[$k][0]) { $specs_html .= '<span>'.esc_html($all_meta[$k][0]).'</span> • '; $c++; } }
                    ?>
                        <div class="offer-card-v2">
                            <div class="card-img">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="card-specs-mini"><?php echo $specs_html ? rtrim($specs_html, ' • ') : 'Ver detalhes'; ?></div>
                                <div class="card-price-block">
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