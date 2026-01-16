<?php
get_header(); 
$term = get_queried_object(); // Pega a categoria atual

// 1. Carrega filtros disponíveis para ESTA categoria
$available_filters = function_exists('crns_get_sidebar_filters') ? crns_get_sidebar_filters($term->term_id) : [];

// Inputs atuais para manter preenchido
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$filter_marca = $_GET['marca'] ?? [];
?>

<div class="content-area offers-layout" style="background-color: #f4f6f8; padding-top: 20px;">
    <div class="container container-flex">
        
<aside class="offers-sidebar">
    <div class="filter-box">
        <h3>🔍 Filtros</h3>
        
        <?php 
        $action_url = is_tax() ? get_term_link($term) : esc_url( get_permalink() );
        ?>
        <form action="<?php echo $action_url; ?>" method="GET">
            
            <?php if ( ! get_option('permalink_structure') && !is_tax() ) : ?>
                <input type="hidden" name="page_id" value="<?php echo get_queried_object_id(); ?>">
            <?php endif; ?>

            <div class="filter-group active">
                <h4>Categoria</h4>
                <div class="filter-options" style="display:block !important"> <select name="<?php echo is_tax() ? 'filtro_cat_disabled' : 'filtro_cat'; ?>" 
                            onchange="this.form.submit()" 
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:16px;"
                            <?php if(is_tax()) echo 'disabled style="background:#eee"'; ?>>
                        <option value="">Todas</option>
                        <?php 
                        $cats = get_terms(['taxonomy' => 'tipo_produto', 'hide_empty' => true]);
                        $current_cat = isset($_GET['filtro_cat']) ? $_GET['filtro_cat'] : (is_tax() ? $term->slug : '');
                        foreach($cats as $cat): ?>
                            <option value="<?php echo $cat->slug; ?>" <?php selected($current_cat, $cat->slug); ?>>
                                <?php echo $cat->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(is_tax()): ?><small style="color:#999; display:block; margin-top:5px;">Você está em <?php echo $term->name; ?></small><?php endif; ?>
                </div>
            </div>

            <button type="button" id="mobile-filter-toggle" class="btn-mobile-toggle">
                <span class="dashicons dashicons-filter"></span> Filtrar por Preço, Marca e Specs
            </button>

            <div id="secondary-filters" class="secondary-filters-wrapper">
                
                <?php 
                $perfis = function_exists('crns_get_valid_classifications') ? crns_get_valid_classifications(is_tax() ? $term->slug : $current_cat) : [];
                if( !empty($perfis) ):
                    $class_active = !empty($filter_class) ? 'active' : ''; 
                ?>
                <div class="filter-group <?php echo $class_active; ?>">
                    <h4>Perfil / Tipo</h4>
                    <div class="filter-options">
                        <?php foreach($perfis as $p): ?>
                            <label><input type="checkbox" name="classificacao[]" value="<?php echo $p->slug; ?>" <?php if(isset($_GET['classificacao']) && in_array($p->slug, (array)$_GET['classificacao'])) echo 'checked'; ?>> <?php echo $p->name; ?></label>
                        <?php endforeach; ?>
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
                    </div>
                </div>

                <?php $marca_active = !empty($filter_marca) ? 'active' : ''; ?>
                <div class="filter-group <?php echo $marca_active; ?>">
                    <h4>Marca</h4>
                    <div class="filter-options">
                        <?php 
                        $marcas = get_terms(['taxonomy' => 'marca', 'hide_empty' => true]);
                        if($marcas && !is_wp_error($marcas)): foreach($marcas as $m): ?>
                            <label><input type="checkbox" name="marca[]" value="<?php echo $m->slug; ?>" <?php if(in_array($m->slug, $filter_marca)) echo 'checked'; ?>> <?php echo $m->name; ?></label>
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <?php if(!empty($available_filters)): ?>
                    <?php foreach($available_filters as $param => $data): 
                        $selected = isset($_GET[$param]) ? $_GET[$param] : [];
                        $is_open = !empty($selected) ? 'active' : '';
                    ?>
                    <div class="filter-group <?php echo $is_open; ?>">
                        <h4><?php echo esc_html($data['label']); ?></h4>
                        <div class="filter-options">
                            <?php foreach($data['options'] as $option): ?>
                                <label><input type="checkbox" name="<?php echo $param; ?>[]" value="<?php echo esc_attr($option); ?>" <?php if(in_array($option, $selected)) echo 'checked'; ?>> <?php echo esc_html($option); ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div style="padding-top:15px;">
                    <button type="submit" class="btn-filter">Aplicar Filtros</button>
                    <a href="<?php echo $action_url; ?>" class="btn-clear">Limpar</a>
                </div>
            </div> </form>
    </div>
</aside>

        <div class="offers-content">
            <header class="offers-header-top">
                <h1><?php single_term_title(); ?></h1>
                <span><?php echo $wp_query->found_posts; ?> produtos</span>
            </header>

            <div class="offers-grid">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
                     // ... (Bloco do Card igual ao que já fizemos) ...
                     $price = get_post_meta( get_the_ID(), '_crns_price', true );
                     $old_price = get_post_meta( get_the_ID(), '_crns_old_price', true );
                     $link = get_post_meta( get_the_ID(), '_crns_affiliate_link', true );
                     $all_meta = get_post_meta(get_the_ID());
                     $specs_html = ''; $c=0;
                     $priority = ['_crns_cpu', '_crns_ram', '_crns_storage', '_crns_screen', '_crns_print_tech', '_crns_camera_main'];
                     foreach($priority as $k) { if($c>=2) break; if(isset($all_meta[$k][0]) && $all_meta[$k][0]) { $specs_html .= '<span>'.esc_html($all_meta[$k][0]).'</span> • '; $c++; } }
                     
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
                                <span class="card-price">R$ <?php echo number_format((float)$price, 2, ',', '.'); ?></span>
                            </div>
                        </div>
                        <div class="card-action"><a href="<?php the_permalink(); ?>" class="btn-buy-v2 btn-outline">Ver Detalhes</a></div>
                    </div>
                <?php endwhile; the_posts_pagination(); else : echo '<p style="padding:20px;">Nada encontrado.</p>'; endif; ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>