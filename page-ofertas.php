<?php
/* Template Name: Página de Ofertas */
get_header(); 

// 1. Captura inputs
$filter_cat = isset($_GET['filtro_cat']) ? $_GET['filtro_cat'] : ''; // Atenção: filtro_cat
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$filter_marca = isset($_GET['marca']) ? $_GET['marca'] : [];

// 2. Descobre ID da Categoria
$current_term_id = 0;
if ( $filter_cat ) {
    $term_obj = get_term_by('slug', $filter_cat, 'tipo_produto');
    if ($term_obj) $current_term_id = $term_obj->term_id;
}

// 3. Busca Filtros Disponíveis
$available_filters = function_exists('crns_get_sidebar_filters') ? crns_get_sidebar_filters($current_term_id) : [];

// 4. MONTA A QUERY PERSONALIZADA (WP_Query)
// Como o functions.php não interfere mais aqui, montamos a lógica completa.
$args = array(
    'post_type'      => 'review',
    'posts_per_page' => 24,
    'meta_query'     => array('relation' => 'AND'),
    'tax_query'      => array('relation' => 'AND'),
    'orderby'        => 'meta_value_num',
    'meta_key'       => '_crns_price',
    'order'          => 'ASC'
);

// Filtro Preço
$args['meta_query'][] = array('key' => '_crns_price', 'compare' => 'EXISTS');
if ( $min_price && $max_price ) {
    $args['meta_query'][] = array(
        'key' => '_crns_price',
        'value' => array( $min_price, $max_price ),
        'type' => 'NUMERIC', 'compare' => 'BETWEEN'
    );
}

// Filtro Categoria
if ( $filter_cat ) {
    $args['tax_query'][] = array(
        'taxonomy' => 'tipo_produto',
        'field'    => 'slug',
        'terms'    => $filter_cat,
    );
}

// Filtro Marca
if ( !empty($filter_marca) ) {
    $args['tax_query'][] = array(
        'taxonomy' => 'marca',
        'field'    => 'slug',
        'terms'    => $filter_marca,
    );
}

// 5. FILTROS DINÂMICOS (PROCESSAMENTO MANUAL NA PÁGINA)
foreach($_GET as $param => $values) {
    // Detecta parâmetros f_ (ex: f_cpu)
    if (strpos($param, 'f_') === 0 && !empty($values)) {
        $slug = str_replace('f_', '', $param);
        
        $sub_query = array('relation' => 'OR');
        
        // Se for array (ex: f_cpu[0]) ou string
        if(is_array($values)) {
            foreach($values as $v) {
                $v = urldecode($v); // Limpa %20
                $sub_query[] = array('key' => '_crns_' . $slug, 'value' => $v, 'compare' => 'LIKE');
                $sub_query[] = array('key' => '_spec_' . $slug, 'value' => $v, 'compare' => 'LIKE');
            }
        } else {
            $values = urldecode($values);
            $sub_query[] = array('key' => '_crns_' . $slug, 'value' => $values, 'compare' => 'LIKE');
            $sub_query[] = array('key' => '_spec_' . $slug, 'value' => $values, 'compare' => 'LIKE');
        }
        
        $args['meta_query'][] = $sub_query;
    }
}

$offers = new WP_Query( $args );
?>

<div class="content-area offers-layout" style="background-color: #f4f6f8; padding-top: 20px;">
    <div class="container container-flex">
        
        <aside class="offers-sidebar">
            <div class="filter-box">
                <h3>🔍 Filtros</h3>
                
                <form action="<?php echo esc_url( get_permalink() ); ?>" method="GET">
                    
                    <?php if ( ! get_option('permalink_structure') ) : ?>
                        <input type="hidden" name="page_id" value="<?php echo get_queried_object_id(); ?>">
                    <?php endif; ?>

                    <div class="filter-group">
                        <h4>Categoria</h4>
                        <select name="filtro_cat" onchange="this.form.submit()" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                            <option value="">Todas</option>
                            <?php 
                            $cats = get_terms(['taxonomy' => 'tipo_produto', 'hide_empty' => true]);
                            foreach($cats as $cat): ?>
                                <option value="<?php echo $cat->slug; ?>" <?php selected($filter_cat, $cat->slug); ?>>
                                    <?php echo $cat->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <h4>Faixa de Preço</h4>
                        <div class="price-inputs">
                            <input type="number" name="min_price" placeholder="Mín" value="<?php echo esc_attr($min_price); ?>">
                            <input type="number" name="max_price" placeholder="Máx" value="<?php echo esc_attr($max_price); ?>">
                        </div>
                    </div>

                    <div class="filter-group">
                        <h4>Marca</h4>
                        <?php 
                        $marcas = get_terms(['taxonomy' => 'marca', 'hide_empty' => true]);
                        if($marcas && !is_wp_error($marcas)): foreach($marcas as $m): ?>
                            <label>
                                <input type="checkbox" name="marca[]" value="<?php echo $m->slug; ?>" <?php if(in_array($m->slug, $filter_marca)) echo 'checked'; ?>>
                                <?php echo $m->name; ?>
                            </label>
                        <?php endforeach; endif; ?>
                    </div>

                    <?php if(!empty($available_filters)): ?>
                        <?php foreach($available_filters as $param => $data): 
                            $selected = isset($_GET[$param]) ? $_GET[$param] : [];
                        ?>
                        <div class="filter-group">
                            <h4><?php echo esc_html($data['label']); ?></h4>
                            <?php foreach($data['options'] as $option): ?>
                                <label>
                                    <input type="checkbox" name="<?php echo $param; ?>[]" value="<?php echo esc_attr($option); ?>" <?php if(in_array($option, $selected)) echo 'checked'; ?>>
                                    <?php echo esc_html($option); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <button type="submit" class="btn-filter">Aplicar Filtros</button>
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn-clear">Limpar</a>
                </form>
            </div>
        </aside>

        <div class="offers-content">
            <header class="offers-header-top">
                <h1>
                    <?php echo $filter_cat ? 'Ofertas de ' . ucfirst($filter_cat) : 'Ofertas de Tecnologia'; ?>
                </h1>
                <span><?php echo $offers->found_posts; ?> produtos encontrados</span>
            </header>

            <div class="offers-grid">
                <?php if( $offers->have_posts() ): ?>
                    <?php while( $offers->have_posts() ) : $offers->the_post();
                        $price = get_post_meta( get_the_ID(), '_crns_price', true );
                        $old_price = get_post_meta( get_the_ID(), '_crns_old_price', true );
                        $link = get_post_meta( get_the_ID(), '_crns_affiliate_link', true );
                        
                        // Specs visual
                        $all_meta = get_post_meta(get_the_ID());
                        $specs_html = ''; $c = 0;
                        $priority = ['_crns_cpu', '_crns_ram', '_crns_storage', '_crns_screen', '_crns_print_tech', '_crns_camera_main'];
                        
                        foreach($priority as $key) {
                            if($c >= 2) break;
                            if(isset($all_meta[$key][0]) && $all_meta[$key][0]) {
                                $specs_html .= '<span>' . esc_html($all_meta[$key][0]) . '</span> • ';
                                $c++;
                            }
                        }
                        
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
                                <span class="card-price">R$ <?php echo number_format((float)$price, 2, ',', '.'); ?></span>
                                <span class="card-installments">em até 10x sem juros</span>
                            </div>
                        </div>

                        <div class="card-action">
                            <?php if($link): ?>
                                <a href="<?php echo esc_url($link); ?>" target="_blank" class="btn-buy-v2">VER OFERTA</a>
                            <?php else: ?>
                                <a href="<?php the_permalink(); ?>" class="btn-buy-v2 btn-outline">DETALHES</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else: ?>
                    <div class="no-results" style="grid-column: 1 / -1; text-align:center; padding: 40px;">
                        <p style="font-size:1.2rem">🚫 Nenhum produto encontrado.</p>
                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn-clear">Limpar Filtros</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>