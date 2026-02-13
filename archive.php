<?php get_header(); ?>

<div id="content" class="site-content home-feed">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <header class="archive-header-custom"
                style="background:#f7f7f7; padding: 40px 0; margin-bottom: 30px; border-bottom:1px solid #eaeaea;">
                <div class="container">
                    <?php
                    $title = get_the_archive_title();
                    // Limpa palavras desnecessárias que o WordPress coloca por padrão
                    $title = preg_replace('/^Categoria: |^Marca: |^Classificação: |^Arquivos: /', '', $title);
                    ?>
                    <h1 class="archive-title" style="margin:0; font-size: 2rem; color: #333; font-weight:700;">
                        <?php echo wp_kses_post($title); ?>
                    </h1>
                    <span style="display:block; margin-top:10px; color:#666; font-size: 1.1rem;">
                        <?php global $wp_query;
                        echo $wp_query->found_posts; ?> resultados encontrados
                    </span>
                </div>
            </header>

            <div class="container">
                <?php if (have_posts()): ?>

                    <div class="offers-grid ml-grid-layout home-grid">
                        <?php while (have_posts()):
                            the_post();

                            // Lógica de Preço
                            $price_raw = get_post_meta(get_the_ID(), '_crns_price', true);
                            $old_price_raw = get_post_meta(get_the_ID(), '_crns_old_price', true);

                            $price_num = str_replace(',', '.', str_replace('.', '', $price_raw));
                            $old_price_num = str_replace(',', '.', str_replace('.', '', $old_price_raw));

                            // Lógica de Desconto
                            $discount_html = '';
                            if ($old_price_num > $price_num && $old_price_num > 0) {
                                $porc = round((($old_price_num - $price_num) / $old_price_num) * 100);
                                $discount_html = '<span class="discount-pill">-' . $porc . '%</span>';
                            }
                            ?>
                            <div class="offer-card-v2 ml-card">
                                <a href="<?php the_permalink(); ?>" class="ml-card-link">
                                    <div class="card-img">
                                        <?php echo $discount_html; ?>
                                        <?php
                                        // OTIMIZAÇÃO: Lazy Load e Alt Text
                                        the_post_thumbnail('large', array(
                                            'loading' => 'lazy',
                                            'alt' => get_the_title()
                                        ));
                                        ?>
                                    </div>

                                    <div class="card-info">
                                        <h3 class="card-title"><?php the_title(); ?></h3>

                                        <div class="card-price-block">
                                            <?php if ($old_price_num > $price_num): ?>
                                                <span class="card-old-price">R$
                                                    <?php echo number_format((float) $old_price_num, 2, ',', '.'); ?></span>
                                            <?php endif; ?>

                                            <div class="ml-price-row">
                                                <span class="card-price">R$
                                                    <?php echo number_format((float) $price_num, 2, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="crns-pagination" style="margin-top: 40px; text-align:center;">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '<span class="dashicons dashicons-arrow-left-alt2"></span> Anterior',
                            'next_text' => 'Próxima <span class="dashicons dashicons-arrow-right-alt2"></span>',
                        ));
                        ?>
                    </div>

                <?php else: ?>

                    <div class="no-results" style="text-align:center; padding: 60px 20px;">
                        <span class="dashicons dashicons-warning"
                            style="font-size: 48px; color: #ccc; margin-bottom:15px;"></span>
                        <p style="font-size: 1.2rem; color:#666;">Nenhum conteúdo encontrado nesta página.</p>
                        <a href="<?php echo esc_url(home_url('/ofertas')); ?>" class="btn-hero-primary"
                            style="display:inline-block; margin-top:20px;">Ver todas as ofertas</a>
                    </div>

                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php get_footer(); ?>