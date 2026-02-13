<?php
get_header();
$term = get_queried_object(); // Pega a categoria atual

// 1. Carrega filtros disponíveis para ESTA categoria
$available_filters = function_exists('crns_get_sidebar_filters') ? crns_get_sidebar_filters($term->term_id) : [];

// Inputs atuais para manter preenchido
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$filter_marca = $_GET['marca'] ?? [];
$filter_class = $_GET['classificacao'] ?? [];
if (!is_array($filter_class) && !empty($filter_class)) {
    $filter_class = array($filter_class);
}

$action_url = get_term_link($term);
?>

<div class="content-area offers-layout ml-layout">

    <div class="ml-mobile-static-bar">
        <span class="results-count"><?php echo $wp_query->found_posts; ?> resultados</span>
        <button id="trigger-mobile-filter" class="ml-filter-btn" aria-label="Abrir Filtros">
            Filtrar <span class="dashicons dashicons-filter"></span>
        </button>
    </div>

    <div class="container container-flex">

        <aside class="offers-sidebar" id="mobile-filter-modal">

            <div class="mobile-modal-header">
                <h3>Filtrar</h3>
                <button id="close-mobile-filter" class="close-btn" aria-label="Fechar">&times;</button>
            </div>

            <div class="filter-box">
                <form action="<?php echo esc_url($action_url); ?>" method="GET">

                    <div id="secondary-filters" class="secondary-filters-wrapper" style="display:block !important;">

                        <?php
                        $perfis = function_exists('crns_get_valid_classifications') ? crns_get_valid_classifications($term->slug) : [];
                        if (!empty($perfis)):
                            $class_active = !empty($filter_class) ? 'active' : '';
                            ?>
                            <div class="filter-group <?php echo $class_active; ?>">
                                <h4>Perfil / Tipo</h4>
                                <div class="filter-options">
                                    <?php foreach ($perfis as $p): ?>
                                        <label><input type="checkbox" name="classificacao[]"
                                                value="<?php echo esc_attr($p->slug); ?>" <?php if (in_array($p->slug, $filter_class))
                                                       echo 'checked'; ?>> <?php echo esc_html($p->name); ?></label>
                                    <?php endforeach; ?>
                                    <div class="filter-actions-small">
                                        <button type="submit" class="btn-apply-small">Aplicar</button>
                                        <button type="button" class="btn-clear-small"
                                            onclick="clearFilterGroup(this)">Limpar</button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php $price_active = (!empty($min_price) || !empty($max_price)) ? 'active' : ''; ?>
                        <div class="filter-group <?php echo $price_active; ?>">
                            <h4>Faixa de Preço</h4>
                            <div class="filter-options">
                                <div class="price-inputs">
                                    <input type="number" name="min_price" placeholder="Mín"
                                        value="<?php echo esc_attr($min_price); ?>">
                                    <input type="number" name="max_price" placeholder="Máx"
                                        value="<?php echo esc_attr($max_price); ?>">
                                </div>
                                <div class="filter-actions-small">
                                    <button type="submit" class="btn-apply-small">Aplicar</button>
                                    <button type="button" class="btn-clear-small"
                                        onclick="clearFilterGroup(this)">Limpar</button>
                                </div>
                            </div>
                        </div>

                        <?php $marca_active = !empty($filter_marca) ? 'active' : ''; ?>
                        <div class="filter-group <?php echo $marca_active; ?>">
                            <h4>Marca</h4>
                            <div class="filter-options">
                                <?php
                                $marcas = get_terms(['taxonomy' => 'marca', 'hide_empty' => true]);
                                if ($marcas && !is_wp_error($marcas)):
                                    foreach ($marcas as $m): ?>
                                        <label><input type="checkbox" name="marca[]" value="<?php echo esc_attr($m->slug); ?>"
                                                <?php if (in_array($m->slug, $filter_marca))
                                                    echo 'checked'; ?>>
                                            <?php echo esc_html($m->name); ?></label>
                                    <?php endforeach; endif; ?>
                                <div class="filter-actions-small">
                                    <button type="submit" class="btn-apply-small">Aplicar</button>
                                    <button type="button" class="btn-clear-small"
                                        onclick="clearFilterGroup(this)">Limpar</button>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($available_filters)): ?>
                            <?php foreach ($available_filters as $param => $data):
                                $selected = isset($_GET[$param]) ? $_GET[$param] : [];
                                $is_open = !empty($selected) ? 'active' : '';
                                ?>
                                <div class="filter-group <?php echo $is_open; ?>">
                                    <h4><?php echo esc_html($data['label']); ?></h4>
                                    <div class="filter-options">
                                        <?php foreach ($data['options'] as $option): ?>
                                            <label><input type="checkbox" name="<?php echo esc_attr($param); ?>[]"
                                                    value="<?php echo esc_attr($option); ?>" <?php if (in_array($option, $selected))
                                                           echo 'checked'; ?>> <?php echo esc_html($option); ?></label>
                                        <?php endforeach; ?>
                                        <div class="filter-actions-small">
                                            <button type="submit" class="btn-apply-small">Aplicar</button>
                                            <button type="button" class="btn-clear-small"
                                                onclick="clearFilterGroup(this)">Limpar</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div style="padding-top:15px; padding-bottom: 50px;">
                            <button type="submit" class="btn-filter">Aplicar Todos os Filtros</button>
                            <a href="<?php echo esc_url($action_url); ?>" class="btn-clear">Limpar Tudo</a>
                        </div>

                    </div>
                </form>
            </div>
        </aside>

        <div class="mobile-filter-overlay" id="filter-overlay"></div>

        <div class="offers-content">
            <header class="offers-header-top desktop-only">
                <h1><?php single_term_title(); ?></h1>
                <span><?php echo $wp_query->found_posts; ?> resultados</span>
            </header>

            <div class="offers-grid ml-grid-layout">
                <?php if (have_posts()):
                    while (have_posts()):
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
                <div class="no-results" style="grid-column: 1 / -1; text-align:center; padding: 60px 20px;">
                    <span class="dashicons dashicons-warning"
                        style="font-size: 48px; color: #ccc; margin-bottom:15px;"></span>
                    <p style="font-size: 1.2rem; color:#666;">Nenhum produto encontrado com estes filtros.</p>
                    <a href="<?php echo esc_url($action_url); ?>" class="btn-clear"
                        style="display:inline-block; margin-top:15px;">Limpar Filtros</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal de Filtros
        const trigger = document.getElementById('trigger-mobile-filter');
        const close = document.getElementById('close-mobile-filter');
        const modal = document.getElementById('mobile-filter-modal');
        const overlay = document.getElementById('filter-overlay');

        function toggleModal() {
            if (!modal) return;
            modal.classList.toggle('open');
            overlay.classList.toggle('open');
            document.body.style.overflow = modal.classList.contains('open') ? 'hidden' : '';
        }

        if (trigger) trigger.addEventListener('click', toggleModal);
        if (close) close.addEventListener('click', toggleModal);
        if (overlay) overlay.addEventListener('click', toggleModal);
    });

    // Função para limpar apenas um grupo de filtros
    function clearFilterGroup(btn) {
        var group = btn.closest('.filter-group');
        var inputs = group.querySelectorAll('input');
        inputs.forEach(function (input) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else if (input.type === 'number' || input.type === 'text') {
                input.value = '';
            }
        });
    }
</script>

<?php get_footer(); ?>