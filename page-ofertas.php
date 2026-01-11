<?php
/* Template Name: Página de Ofertas */
get_header(); 

// 1. Captura filtros da URL
$filter_cat = isset($_GET['categoria']) ? $_GET['categoria'] : ''; // Novo: Filtro de Categoria
$filter_marcas = isset($_GET['marca']) ? $_GET['marca'] : array();
$filter_ram = isset($_GET['ram']) ? $_GET['ram'] : array();
$filter_ssd = isset($_GET['ssd']) ? $_GET['ssd'] : array();
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// 2. Configuração Dinâmica (O Cérebro)
// Se houver uma categoria selecionada, carregamos os filtros específicos dela.
// Se não, carregamos apenas o básico (Marca/Preço).
$enabled_filters = ['marca']; // Padrão
if( $filter_cat && function_exists('crns_get_filters_config') ) {
    $enabled_filters = crns_get_filters_config($filter_cat);
}

// 3. Monta a Query
$args = array(
    'post_type'      => 'review',
    'posts_per_page' => 24,
    'meta_query'     => array('relation' => 'AND'),
    'tax_query'      => array('relation' => 'AND'),
    'orderby'        => 'meta_value_num',
    'meta_key'       => '_crns_price',
    'order'          => 'ASC'
);

// Filtro: Preço Existe
$args['meta_query'][] = array('key' => '_crns_price', 'compare' => 'EXISTS');

// Filtro: Faixa de Preço
if ( $min_price && $max_price ) {
    $args['meta_query'][] = array(
        'key' => '_crns_price',
        'value' => array( $min_price, $max_price ),
        'type' => 'NUMERIC', 'compare' => 'BETWEEN'
    );
}

// Filtro: Categoria (Tipo de Produto) - NOVO
if ( $filter_cat ) {
    $args['tax_query'][] = array(
        'taxonomy' => 'tipo_produto',
        'field'    => 'slug',
        'terms'    => $filter_cat,
    );
}

// Filtro: Marcas
if ( !empty($filter_marcas) ) {
    $args['tax_query'][] = array(
        'taxonomy' => 'marca',
        'field'    => 'slug',
        'terms'    => $filter_marcas,
    );
}

// Filtros Técnicos (Só aplicados se a categoria ativa permitir)
if ( in_array('ram', $enabled_filters) && !empty($filter_ram) ) {
    $ram_query = array('relation' => 'OR');
    foreach($filter_ram as $ram) $ram_query[] = array('key' => '_crns_ram', 'value' => $ram, 'compare' => 'LIKE');
    $args['meta_query'][] = $ram_query;
}

if ( in_array('ssd', $enabled_filters) && !empty($filter_ssd) ) {
    $ssd_query = array('relation' => 'OR');
    foreach($filter_ssd as $ssd) $ssd_query[] = array('key' => '_crns_storage', 'value' => $ssd, 'compare' => 'LIKE');
    $args['meta_query'][] = $ssd_query;
}

$offers = new WP_Query( $args );
?>

<div class="content-area offers-layout" style="background-color: #f4f6f8; padding-top: 20px;">
    <div class="container container-flex">
        
        <aside class="offers-sidebar">
            <div class="filter-box">
                <h3>🔍 Filtros</h3>
                <form action="<?php echo get_permalink(); ?>" method="GET">
                    
                    <div class="filter-group">
                        <h4>Categoria</h4>
                        <select name="categoria" onchange="this.form.submit()" style="width:100%; padding:8px;">
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

                    <?php if(in_array('marca', $enabled_filters)): ?>
                    <div class="filter-group">
                        <h4>Marca</h4>
                        <?php 
                        $marcas = get_terms(['taxonomy' => 'marca', 'hide_empty' => true]);
                        if($marcas): foreach($marcas as $marca): ?>
                            <label>
                                <input type="checkbox" name="marca[]" value="<?php echo $marca->slug; ?>" <?php if(in_array($marca->slug, $filter_marcas)) echo 'checked'; ?>>
                                <?php echo $marca->name; ?>
                            </label>
                        <?php endforeach; endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if(in_array('ram', $enabled_filters)): ?>
                    <div class="filter-group">
                        <h4>Memória RAM</h4>
                        <?php $rams = ['4GB', '8GB', '16GB', '32GB']; foreach($rams as $r): ?>
                            <label><input type="checkbox" name="ram[]" value="<?php echo $r; ?>" <?php if(in_array($r, $filter_ram)) echo 'checked'; ?>> <?php echo $r; ?></label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if(in_array('ssd', $enabled_filters) || in_array('storage', $enabled_filters)): ?>
                    <div class="filter-group">
                        <h4>Armazenamento</h4>
                        <?php $ssds = ['256GB', '512GB', '1TB']; foreach($ssds as $s): ?>
                            <label><input type="checkbox" name="ssd[]" value="<?php echo $s; ?>" <?php if(in_array($s, $filter_ssd)) echo 'checked'; ?>> <?php echo $s; ?></label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn-filter">Aplicar Filtros</button>
                    <a href="<?php echo get_permalink(); ?>" class="btn-clear">Limpar</a>
                </form>
            </div>
        </aside>

        <div class="offers-content">
            <header class="offers-header-top">
                <h1>
                    <?php echo $filter_cat ? 'Ofertas de ' . ucfirst($filter_cat) : 'Ofertas de Tecnologia'; ?>
                </h1>
                <span><?php echo $offers->found_posts; ?> produtos</span>
            </header>

            <div class="offers-grid">
                <?php if( $offers->have_posts() ): ?>
                    <?php while( $offers->have_posts() ) : $offers->the_post();
                        // Meta dados básicos
                        $price = get_post_meta( get_the_ID(), '_crns_price', true );
                        $old_price = get_post_meta( get_the_ID(), '_crns_old_price', true );
                        $link = get_post_meta( get_the_ID(), '_crns_affiliate_link', true );
                        
                        // Lógica Dinâmica de Specs do Card
                        // 1. Descobre a categoria do item
                        $terms = get_the_terms( get_the_ID(), 'tipo_produto' );
                        $cat_slug = ($terms && !is_wp_error($terms)) ? $terms[0]->slug : 'notebooks';
                        
                        // 2. Pega os campos configurados para essa categoria (do functions.php)
                        $specs_keys = function_exists('crns_get_specs_by_category') ? crns_get_specs_by_category($cat_slug) : [];
                        
                        // 3. Monta o HTML das specs (pega só as 2 primeiras relevantes)
                        $specs_html = '';
                        $count = 0;
                        foreach($specs_keys as $key) {
                            if($count >= 2) break; // Limite de 2 itens no card
                            $val = get_post_meta(get_the_ID(), $key, true);
                            if($val) {
                                $specs_html .= '<span>' . esc_html($val) . '</span> • ';
                                $count++;
                            }
                        }
                        $specs_html = rtrim($specs_html, ' • '); // Remove último separador

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
                            
                            <div class="card-specs-mini">
                                <?php echo $specs_html ? $specs_html : 'Ver detalhes'; ?>
                            </div>

                            <div class="card-price-block">
                                <?php if($old_price > $price): ?>
                                    <span class="card-old-price">R$ <?php echo number_format((float)$old_price, 2, ',', '.'); ?></span>
                                <?php endif; ?>
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
                        <a href="<?php echo get_permalink(); ?>" class="btn-clear">Limpar Filtros</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>