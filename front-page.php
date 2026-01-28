<?php get_header(); ?>

<section class="hero-split">
    <div class="container">
        <div class="split-wrapper">

            <div class="split-content">
                <h1>Compre o Equipamento Certo<br><span class="highlight-text">Pelo Menor Preço</span></h1>
                <p class="hero-description">
                    Analisamos e comparamos <strong>Notebooks</strong>, <strong>Celulares</strong> e <strong>Periféricos </strong>
                    para indicar apenas o que realmente vale a pena.
                </p>

                <div class="hero-buttons">
                    <a href="<?php echo site_url('/ofertas'); ?>" class="btn-hero-primary">
                        <span class="dashicons dashicons-tag"></span> Ver Ofertas
                    </a>

                    <a href="https://chat.whatsapp.com/L1gN8KvrxVH63AY591Rrbs" target="_blank" class="btn-hero-whatsapp">
                        <span class="dashicons dashicons-whatsapp"></span> Entrar nos Grupos
                    </a>
                </div>

                <p class="hero-note">Receba descontos reais diretamente no celular.</p>
            </div>

            <div class="split-visuals">
                <div class="crns-slider-box">
                    <div class="hero-slider-header">
                        <h3>🔥 Melhores do dia</h3>
                    </div>

                    <div class="swiper heroSwiper">
                        <div class="swiper-wrapper">
                            <?php
                            $hero = new WP_Query(array('post_type' => 'review', 'posts_per_page' => 5, 'ignore_sticky_posts' => 1));
                            if ($hero->have_posts()) : while ($hero->have_posts()) : $hero->the_post();
                                    $bg_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                                    $price = get_post_meta(get_the_ID(), '_crns_price', true);
                            ?>
                                    <div class="swiper-slide">
                                        <a href="<?php the_permalink(); ?>" class="slide-card">
                                            <div class="slide-top">
                                                <span class="slide-cat">
                                                    <?php
                                                    $cats = get_the_terms(get_the_ID(), 'tipo_produto');
                                                    if ($cats) echo $cats[0]->name;
                                                    ?>
                                                </span>
                                                <h4><?php echo wp_trim_words(get_the_title(), 6); ?></h4>
                                            </div>

                                            <div class="slide-image">
                                                <img src="<?php echo esc_url($bg_url); ?>" alt="<?php the_title_attribute(); ?>">
                                            </div>

                                            <?php if ($price): ?>
                                                <div class="slide-bottom">
                                                    <span class="slide-price-btn">R$ <?php echo $price; ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                            <?php endwhile;
                                wp_reset_postdata();
                            endif; ?>
                        </div>

                        <div class="swiper-pagination custom-pagination"></div>
                        <div class="swiper-button-prev-custom"><span class="dashicons dashicons-arrow-left-alt2"></span></div>
                        <div class="swiper-button-next-custom"><span class="dashicons dashicons-arrow-right-alt2"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            var swiper = new Swiper(".heroSwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next-custom",
                    prevEl: ".swiper-button-prev-custom",
                },
                breakpoints: {
                    640: { slidesPerView: 2, spaceBetween: 15 },
                    1024: { slidesPerView: 2, spaceBetween: 20 },
                },
            });
        } else {
            console.warn('Swiper JS não foi carregado.');
        }
    });
</script>

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
                <span class="dashicons dashicons-printer" style="color: #e3007e;"></span><span>Tanque de Tinta</span>
            </a>
            <a href="<?php echo site_url('/ofertas/?filtro_cat=impressoras&classificacao=wifi'); ?>" class="profile-card">
                <span class="dashicons dashicons-networking"></span><span>Wi-Fi</span>
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

                <div class="offers-grid ml-grid-layout home-grid">
                    <?php
                    $args_home = array(
                        'post_type'      => 'review',
                        'posts_per_page' => 4,
                        'post_status'    => 'publish',
                        'orderby'        => 'date',
                        'order'          => 'DESC'
                    );

                    $home_query = new WP_Query($args_home);

                    if ($home_query->have_posts()):
                        while ($home_query->have_posts()) : $home_query->the_post();

                            $price = get_post_meta(get_the_ID(), '_crns_price', true);
                            $old_price = get_post_meta(get_the_ID(), '_crns_old_price', true);
                            
                            $discount_html = '';
                            if ($old_price > $price && $old_price > 0) {
                                $porc = round((($old_price - $price) / $old_price) * 100);
                                $discount_html = '<span class="discount-pill">-' . $porc . '%</span>';
                            }
                    ?>
                            <div class="offer-card-v2 ml-card">
                                <a href="<?php the_permalink(); ?>" class="ml-card-link">
                                    <div class="card-img">
                                        <?php echo $discount_html; ?>
                                        <?php the_post_thumbnail('large'); ?>
                                    </div>
                                    <div class="card-info">
                                        <h3 class="card-title"><?php the_title(); ?></h3>
                                        <div class="card-price-block">
                                            <?php if ($old_price > $price): ?>
                                                <span class="card-old-price">R$ <?php echo number_format((float)$old_price, 2, ',', '.'); ?></span>
                                            <?php endif; ?>
                                            <div class="ml-price-row">
                                                <span class="card-price">R$ <?php echo number_format((float)$price, 2, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <span class="btn-buy-v2 btn-outline">Ver Análise</span>
                                    </div>
                                </a>
                            </div>

                        <?php endwhile;
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