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
                <h3>Filtrar <?php echo $term->name; ?></h3>
                
                <form action="<?php echo get_term_link($term); ?>" method="GET">
                    
                    <div class="filter-group">
                        <h4>Preço (R$)</h4>
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

                    <button type="submit" class="btn-filter">Aplicar</button>
                    <a href="<?php echo get_term_link($term); ?>" class="btn-clear">Limpar</a>
                </form>
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