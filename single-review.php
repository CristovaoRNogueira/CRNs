<?php get_header(); ?>

<div class="content-area review-page-layout">

    <?php while (have_posts()):
        the_post();

        // 1. Coleta de Dados
        $price_raw = get_post_meta(get_the_ID(), '_crns_price', true);
        $old_price_raw = get_post_meta(get_the_ID(), '_crns_old_price', true);
        $affiliate_link = get_post_meta(get_the_ID(), '_crns_affiliate_link', true);
        $coupon_code = get_post_meta(get_the_ID(), '_crns_coupon_code', true);

        // --- CORREÇÃO DE PREÇO ---
        $price = str_replace(',', '.', str_replace('.', '', $price_raw));
        $old_price = str_replace(',', '.', str_replace('.', '', $old_price_raw));

        // --- LÓGICA DA AVALIAÇÃO (NOTA) ---
        $rating_raw = get_post_meta(get_the_ID(), '_crns_rating', true);
        $rating = $rating_raw ? (float) str_replace(',', '.', $rating_raw) : 0;

        $percent = $rating * 10;
        $dash_array = $percent . ', 100';

        if ($rating >= 9) {
            $rating_label = "Excelente";
            $rating_color = "#4caf50";
        } elseif ($rating >= 7.5) {
            $rating_label = "Muito Bom";
            $rating_color = "#8bc34a";
        } elseif ($rating >= 6) {
            $rating_label = "Bom";
            $rating_color = "#ffc107";
        } elseif ($rating >= 4) {
            $rating_label = "Regular";
            $rating_color = "#ff9800";
        } else {
            $rating_label = "Fraco";
            $rating_color = "#f44336";
        }

        $all_meta = get_post_meta(get_the_ID());

        // Cálculo de Desconto
        $discount_html = '';
        if ($old_price > $price && $old_price > 0) {
            $porc = round((($old_price - $price) / $old_price) * 100);
            $discount_html = '<span class="review-discount-badge">-' . $porc . '% OFF</span>';
        }

        // Specs DESTAQUE
        $hero_specs = [
            'cpu' => ['icon' => 'dashicons-dashboard', 'label' => 'Processador', 'val' => isset($all_meta['_crns_cpu'][0]) ? $all_meta['_crns_cpu'][0] : ''],
            'ram' => ['icon' => 'dashicons-micro', 'label' => 'Memória RAM', 'val' => isset($all_meta['_crns_ram'][0]) ? $all_meta['_crns_ram'][0] : ''],
            'gpu' => ['icon' => 'dashicons-desktop', 'label' => 'Placa de Vídeo', 'val' => isset($all_meta['_crns_gpu'][0]) ? $all_meta['_crns_gpu'][0] : ''],
            'ssd' => ['icon' => 'dashicons-database', 'label' => 'Armazenamento', 'val' => isset($all_meta['_crns_storage'][0]) ? $all_meta['_crns_storage'][0] : ''],
            'screen' => ['icon' => 'dashicons-visibility', 'label' => 'Tela', 'val' => isset($all_meta['_crns_screen'][0]) ? $all_meta['_crns_screen'][0] : ''],
        ];
        ?>

        <div class="crns-review-hero">
            <div class="container">

                <div class="crns-breadcrumb">
                    <a href="<?php echo home_url(); ?>">Home</a>
                    <span class="sep">/</span>
                    <a href="<?php echo home_url('/ofertas'); ?>">Reviews</a>
                    <span class="sep">/</span>
                    <span class="current"><?php the_title(); ?></span>
                </div>

                <div class="review-hero-grid">

                    <div class="hero-gallery">
                        <div class="main-image-wrapper">
                            <?php echo $discount_html; ?>
                            <?php
                            the_post_thumbnail('large', array(
                                'class' => 'hero-main-img',
                                'loading' => 'eager',
                                'fetchpriority' => 'high'
                            ));
                            ?>
                        </div>
                    </div>

                    <div class="hero-info">
                        <div class="hero-badges">
                            <?php
                            $cats = get_the_terms(get_the_ID(), 'tipo_produto');
                            if ($cats)
                                echo '<span class="badge-cat">' . $cats[0]->name . '</span>';
                            ?>
                            <span class="badge-date"><span class="dashicons dashicons-calendar-alt"></span>
                                <?php echo get_the_date('d/m/Y'); ?></span>
                        </div>

                        <h1 class="review-title"><?php the_title(); ?></h1>

                        <div class="hero-quick-specs">
                            <?php foreach ($hero_specs as $key => $data):
                                if ($data['val']): ?>
                                    <div class="quick-spec-item">
                                        <span class="dashicons <?php echo $data['icon']; ?>"></span>
                                        <div class="spec-text">
                                            <small><?php echo $data['label']; ?></small>
                                            <strong><?php echo esc_html($data['val']); ?></strong>
                                        </div>
                                    </div>
                                <?php endif; endforeach; ?>
                        </div>

                        <div class="hero-price-box">
                            <div class="price-header">
                                <span class="best-price-label">Melhor preço hoje</span>
                            </div>

                            <div class="price-values">
                                <?php if ($old_price > $price): ?>
                                    <span class="old-price">De R$
                                        <?php echo number_format((float) $old_price, 2, ',', '.'); ?></span>
                                <?php endif; ?>

                                <div class="current-price-row">
                                    <span class="currency">R$</span>
                                    <span class="value"><?php echo number_format((float) $price, 2, ',', '.'); ?></span>
                                </div>
                            </div>

                            <?php if ($coupon_code): ?>
                                <div class="coupon-area" style="margin-bottom: 15px;">
                                    <div class="coupon-box" onclick="copyCoupon('<?php echo esc_js($coupon_code); ?>', this)">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <span class="dashicons dashicons-tickets-alt"></span>
                                            <span class="coupon-code-text"><?php echo esc_html($coupon_code); ?></span>
                                        </div>
                                        <span class="coupon-action">COPIAR</span>
                                    </div>
                                    <small class="coupon-msg"
                                        style="display:none; color:#28a745; margin-top:5px; font-weight:600;">Cupom
                                        copiado!</small>
                                </div>
                            <?php endif; ?>

                            <div class="cta-actions">
                                <a href="<?php echo $affiliate_link ? esc_url($affiliate_link) : '#'; ?>" target="_blank"
                                    rel="nofollow" class="btn-buy-large pulse-animation">
                                    Ver Oferta <span class="dashicons dashicons-external"></span>
                                </a>
                                <p class="safe-buy"><span class="dashicons dashicons-lock"></span> Compra Segura
                                </p>
                                <p class="safe-buy">⚠️ Preços podem mudar a qualquer momento
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container review-body-container">
            <div class="review-layout-split">

                <article class="review-content-main">

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <div class="full-specs-table">
                        <h3>Ficha Técnica Completa</h3>
                        <table>
                            <tbody>
                                <?php
                                $ignore_keys = [
                                    '_crns_price',
                                    '_crns_old_price',
                                    '_crns_rating',
                                    '_crns_affiliate_link',
                                    '_crns_coupon_code',
                                    '_edit_lock',
                                    '_edit_last',
                                    '_thumbnail_id',
                                    '_wp_page_template'
                                ];

                                foreach ($all_meta as $key => $values) {
                                    $val = isset($values[0]) ? $values[0] : '';
                                    if (empty($val) || in_array($key, $ignore_keys))
                                        continue;

                                    if (strpos($key, '_crns_') === 0 || strpos($key, '_spec_') === 0) {
                                        if (function_exists('crns_format_spec_label')) {
                                            $label = crns_format_spec_label($key);
                                        } else {
                                            $label = ucwords(str_replace(['_crns_', '_spec_', '_'], ['', '', ' '], $key));
                                        }
                                        echo '<tr>';
                                        echo '<td><strong>' . esc_html($label) . '</strong></td>';
                                        echo '<td>' . esc_html($val) . '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </article>

                <aside class="review-sidebar desktop-only">
                    <div class="sticky-sidebar-widget">
                        <h3>Nossa Avaliação</h3>
                        <?php if ($rating > 0): ?>
                            <div class="score-circle">
                                <svg viewBox="0 0 36 36" class="circular-chart">
                                    <path class="circle-bg"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="circle" stroke-dasharray="<?php echo $dash_array; ?>"
                                        stroke="<?php echo $rating_color; ?>"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <text x="18" y="20.35" class="percentage"><?php echo $rating; ?></text>
                                </svg>
                                <p style="color:<?php echo $rating_color; ?>; font-weight:bold;"><?php echo $rating_label; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <p style="text-align:center; color:#777;">Ainda não avaliado</p>
                        <?php endif; ?>

                        <div class="mini-offer-widget">
                            <p>Melhor preço:</p>
                            <span class="mini-price">R$ <?php echo number_format((float) $price, 2, ',', '.'); ?></span>
                            <a href="<?php echo esc_url($affiliate_link); ?>" target="_blank" class="btn-sidebar-buy">Ir
                                para Loja</a>
                        </div>
                    </div>
                </aside>

            </div>
        </div>

        <div class="mobile-sticky-buy-bar">
            <div class="bar-info">
                <span class="bar-label">Melhor preço:</span>
                <span class="bar-price">R$ <?php echo number_format((float) $price, 2, ',', '.'); ?></span>
            </div>
            <a href="<?php echo esc_url($affiliate_link); ?>" target="_blank" class="btn-bar-buy">
                Ver Oferta
            </a>
        </div>

    <?php endwhile; ?>
</div>

<script>
    function copyCoupon(code, element) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(code).then(function () {
                showCopyFeedback(element);
            }, function (err) {
                fallbackCopyTextToClipboard(code, element);
            });
        } else {
            fallbackCopyTextToClipboard(code, element);
        }
    }

    function fallbackCopyTextToClipboard(text, element) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            var successful = document.execCommand('copy');
            if (successful) showCopyFeedback(element);
        } catch (err) {
            console.error('Fallback: Erro', err);
        }
        document.body.removeChild(textArea);
    }

    function showCopyFeedback(element) {
        const actionSpan = element.querySelector('.coupon-action');
        const originalText = actionSpan.innerText;

        actionSpan.innerText = 'COPIADO!';
        actionSpan.style.color = '#fff';
        actionSpan.style.background = '#28a745';
        element.style.borderColor = '#28a745';
        element.style.background = '#e6fffa';

        const msg = element.nextElementSibling;
        if (msg) msg.style.display = 'block';

        setTimeout(function () {
            actionSpan.innerText = originalText;
            actionSpan.style.color = '#28a745';
            actionSpan.style.background = 'rgba(40, 167, 69, 0.1)';
            element.style.borderColor = '#28a745';
            element.style.background = '#fdfdfd';
            if (msg) msg.style.display = 'none';
        }, 3000);
    }
</script>

<?php get_footer(); ?>