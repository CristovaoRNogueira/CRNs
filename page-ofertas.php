<?php
/* Template Name: Página de Ofertas */
get_header();

// 1. Captura inputs
$filter_cat = isset($_GET['filtro_cat']) ? $_GET['filtro_cat'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$filter_marca = isset($_GET['marca']) ? $_GET['marca'] : [];

// Garante array
$filter_class = isset($_GET['classificacao']) ? $_GET['classificacao'] : [];
if (! is_array($filter_class) && ! empty($filter_class)) {
    $filter_class = array($filter_class);
}

// 2. Descobre ID da Categoria
$current_term_id = 0;
if ($filter_cat) {
    $term_obj = get_term_by('slug', $filter_cat, 'tipo_produto');
    if ($term_obj) $current_term_id = $term_obj->term_id;
}

// 3. Busca Filtros
$available_filters = function_exists('crns_get_sidebar_filters') ? crns_get_sidebar_filters($current_term_id) : [];

// 4. MONTA A QUERY
$args = array(
    'post_type'      => 'review',
    'posts_per_page' => 24,
    'meta_query'     => array('relation' => 'AND'),
    'tax_query'      => array('relation' => 'AND'),
    'orderby'        => 'meta_value_num',
    'meta_key'       => '_crns_price',
    'order'          => 'ASC'
);

// Preço
$args['meta_query'][] = array('key' => '_crns_price', 'compare' => 'EXISTS');
if ($min_price && $max_price) {
    $args['meta_query'][] = array(
        'key' => '_crns_price',
        'value' => array($min_price, $max_price),
        'type' => 'NUMERIC',
        'compare' => 'BETWEEN'
    );
}

// Categoria
if ($filter_cat) {
    $args['tax_query'][] = array(
        'taxonomy' => 'tipo_produto',
        'field'    => 'slug',
        'terms'    => $filter_cat,
    );
}

// Marca
if (!empty($filter_marca)) {
    $args['tax_query'][] = array(
        'taxonomy' => 'marca',
        'field'    => 'slug',
        'terms'    => $filter_marca,
    );
}

// Classificação
if (!empty($filter_class)) {
    $args['tax_query'][] = array(
        'taxonomy' => 'classificacao',
        'field'    => 'slug',
        'terms'    => $filter_class,
    );
}

// Filtros Dinâmicos
foreach ($_GET as $param => $values) {
    if (strpos($param, 'f_') === 0 && !empty($values)) {
        $slug = str_replace('f_', '', $param);
        $sub_query = array('relation' => 'OR');
        if (!is_array($values)) $values = array($values);
        foreach ($values as $v) {
            $v = urldecode($v);
            $sub_query[] = array('key' => '_crns_' . $slug, 'value' => $v, 'compare' => 'LIKE');
            $sub_query[] = array('key' => '_spec_' . $slug, 'value' => $v, 'compare' => 'LIKE');
        }
        $args['meta_query'][] = $sub_query;
    }
}

$offers = new WP_Query($args);
?>

<div class="content-area offers-layout ml-layout">

    <div class="ml-mobile-static-bar">
        <span class="results-count"><?php echo $offers->found_posts; ?> resultados</span>
        <button id="trigger-mobile-filter" class="ml-filter-btn">
            Filtrar <span class="dashicons dashicons-filter"></span>
        </button>
    </div>

    <div class="container visual-categories-strip">
        <div class="ml-category-carousel">
            <?php
            $cats_icon_map = [
                'notebooks' => 'dashicons-laptop',
                'smartphones' => 'dashicons-smartphone',
                'impressoras' => 'dashicons-printer',
                'games' => 'dashicons-games',
                'perifericos' => 'dashicons-mouse',
                'monitores' => 'dashicons-desktop'
            ];

            $cat_terms = get_terms(['taxonomy' => 'tipo_produto', 'hide_empty' => true]);

            echo '<a href="' . get_permalink() . '" class="ml-cat-card ' . ($filter_cat == '' ? 'active' : '') . '">';
            echo '<div class="cat-icon-circle"><span class="dashicons dashicons-menu"></span></div>';
            echo '<span class="cat-name">Todas</span>';
            echo '</a>';

            if (!is_wp_error($cat_terms)) {
                foreach ($cat_terms as $ct) {
                    $icon = isset($cats_icon_map[$ct->slug]) ? $cats_icon_map[$ct->slug] : 'dashicons-tag';
                    $is_active = ($filter_cat == $ct->slug) ? 'active' : '';
                    $link = add_query_arg('filtro_cat', $ct->slug, get_permalink());

                    echo '<a href="' . esc_url($link) . '" class="ml-cat-card ' . $is_active . '">';
                    echo '<div class="cat-icon-circle"><span class="dashicons ' . $icon . '"></span></div>';
                    echo '<span class="cat-name">' . esc_html($ct->name) . '</span>';
                    echo '</a>';
                }
            }
            ?>
        </div>
    </div>

    <div class="container container-flex">

        <aside class="offers-sidebar" id="mobile-filter-modal">

            <div class="mobile-modal-header">
                <h3>Filtrar</h3>
                <button id="close-mobile-filter" class="close-btn">&times;</button>
            </div>

            <div class="filter-box">
                <form action="<?php echo esc_url(get_permalink()); ?>" method="GET">
                    <?php if (! get_option('permalink_structure') && !is_tax()) : ?>
                        <input type="hidden" name="page_id" value="<?php echo get_queried_object_id(); ?>">
                    <?php endif; ?>

                    <?php if ($filter_cat): ?>
                        <input type="hidden" name="filtro_cat" value="<?php echo esc_attr($filter_cat); ?>">
                    <?php endif; ?>

                    <div id="secondary-filters" class="secondary-filters-wrapper" style="display:block !important;">
                        
                        <?php
                        $perfis = function_exists('crns_get_valid_classifications') ? crns_get_valid_classifications($filter_cat) : [];
                        if (!empty($perfis)):
                            $class_active = !empty($filter_class) ? 'active' : '';
                        ?>
                            <div class="filter-group <?php echo $class_active; ?>">
                                <h4>Perfil / Tipo</h4>
                                <div class="filter-options">
                                    <?php foreach ($perfis as $p): ?>
                                        <label>
                                            <input type="checkbox" name="classificacao[]" value="<?php echo $p->slug; ?>"
                                                <?php if (in_array($p->slug, (array)$filter_class)) echo 'checked'; ?>>
                                            <?php echo $p->name; ?>
                                        </label>
                                    <?php endforeach; ?>
                                    
                                    <div class="filter-actions-small">
                                        <button type="submit" class="btn-apply-small">Aplicar</button>
                                        <button type="button" class="btn-clear-small" onclick="clearFilterGroup(this)">Limpar</button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php $price_active = (!empty($min_price) || !empty($max_price)) ? 'active' : ''; ?>
                        <div class="filter-group <?php echo $price_active; ?>">
                            <h4>Faixa de Preço</h4>
                            <div class="filter-options">
                                <div class="price-inputs">
                                    <input type="number" name="min_price" placeholder="Mín" value="<?php echo esc_attr($min_price); ?>">
                                    <input type="number" name="max_price" placeholder="Máx" value="<?php echo esc_attr($max_price); ?>">
                                </div>
                                
                                <div class="filter-actions-small">
                                    <button type="submit" class="btn-apply-small">Aplicar</button>
                                    <button type="button" class="btn-clear-small" onclick="clearFilterGroup(this)">Limpar</button>
                                </div>
                            </div>
                        </div>

                        <?php $marca_active = !empty($filter_marca) ? 'active' : ''; ?>
                        <div class="filter-group <?php echo $marca_active; ?>">
                            <h4>Marca</h4>
                            <div class="filter-options">
                                <?php
                                $marcas = get_terms(['taxonomy' => 'marca', 'hide_empty' => true]);
                                if ($marcas && !is_wp_error($marcas)): foreach ($marcas as $m): ?>
                                        <label><input type="checkbox" name="marca[]" value="<?php echo $m->slug; ?>" <?php if (in_array($m->slug, $filter_marca)) echo 'checked'; ?>> <?php echo $m->name; ?></label>
                                <?php endforeach;
                                endif; ?>

                                <div class="filter-actions-small">
                                    <button type="submit" class="btn-apply-small">Aplicar</button>
                                    <button type="button" class="btn-clear-small" onclick="clearFilterGroup(this)">Limpar</button>
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
                                            <label><input type="checkbox" name="<?php echo $param; ?>[]" value="<?php echo esc_attr($option); ?>" <?php if (in_array($option, $selected)) echo 'checked'; ?>> <?php echo esc_html($option); ?></label>
                                        <?php endforeach; ?>

                                        <div class="filter-actions-small">
                                            <button type="submit" class="btn-apply-small">Aplicar</button>
                                            <button type="button" class="btn-clear-small" onclick="clearFilterGroup(this)">Limpar</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div style="padding-top:15px; padding-bottom: 50px;">
                            <button type="submit" class="btn-filter">Aplicar Todos os Filtros</button>
                            <a href="<?php echo esc_url(get_permalink()); ?>" class="btn-clear">Limpar Tudo</a>
                        </div>
                    </div>
                </form>
            </div>
        </aside>

        <div class="mobile-filter-overlay" id="filter-overlay"></div>

        <div class="offers-content">
            <header class="offers-header-top desktop-only">
                <h1><?php echo $filter_cat ? 'Ofertas de ' . ucfirst($filter_cat) : 'Ofertas do Dia'; ?></h1>
                <span><?php echo $offers->found_posts; ?> resultados</span>
            </header>

            <div class="offers-grid ml-grid-layout">
                <?php if ($offers->have_posts()): ?>
                    <?php while ($offers->have_posts()) : $offers->the_post();
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
                                    <?php the_post_thumbnail('medium'); ?>
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
                            </a>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                <?php else: ?>
                    <div class="no-results" style="grid-column: 1 / -1; text-align:center; padding: 40px;">
                        <p>🚫 Nenhum produto encontrado.</p>
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="btn-clear">Limpar Filtros</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

    // Função para limpar apenas o grupo específico e permitir nova aplicação
    function clearFilterGroup(btn) {
        var group = btn.closest('.filter-group');
        var inputs = group.querySelectorAll('input');
        inputs.forEach(function(input) {
            if(input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else if (input.type === 'number' || input.type === 'text') {
                input.value = '';
            }
        });
    }
</script>

<?php get_footer(); ?>