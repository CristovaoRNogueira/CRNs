<?php
get_header(); 
$term = get_queried_object();
$current_slug = $term->slug; 

// Carrega filtros permitidos (usando a função normalizada do functions.php)
$enabled_filters = function_exists('crns_get_filters_config') ? crns_get_filters_config($current_slug) : ['marca'];

// Captura GET
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$filter_marca = $_GET['marca'] ?? [];
$filter_ram = $_GET['ram'] ?? [];
$filter_ssd = $_GET['ssd'] ?? [];
$filter_storage = $_GET['storage'] ?? []; // Novo para celular
?>

<div class="content-area offers-layout" style="background-color: #f4f6f8; padding-top: 20px;">
    <div class="container container-flex">
        
        <aside class="offers-sidebar">
            <div class="filter-box">
                <h3>Filtrar <?php echo $term->name; ?></h3>
                <form action="<?php echo get_term_link($term); ?>" method="GET">
                    
                    <div class="filter-group">
                        <h4>Faixa de Preço</h4>
                        <div class="price-inputs">
                            <input type="number" name="min_price" placeholder="Mín" value="<?php echo esc_attr($min_price); ?>">
                            <input type="number" name="max_price" placeholder="Máx" value="<?php echo esc_attr($max_price); ?>">
                        </div>
                    </div>

                    <?php if (in_array('marca', $enabled_filters)): ?>
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
                    <?php endif; ?>

                    <?php if (in_array('ram', $enabled_filters)): ?>
                    <div class="filter-group">
                        <h4>Memória RAM</h4>
                        <?php 
                        // Se for celular, mostra opções menores
                        $is_phone = (strpos($current_slug, 'celular') !== false || strpos($current_slug, 'smartphone') !== false);
                        $rams = $is_phone ? ['4GB', '6GB', '8GB', '12GB'] : ['4GB', '8GB', '16GB', '32GB'];
                        foreach($rams as $r): ?>
                            <label><input type="checkbox" name="ram[]" value="<?php echo $r; ?>" <?php if(in_array($r, $filter_ram)) echo 'checked'; ?>> <?php echo $r; ?></label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array('ssd', $enabled_filters)): ?>
                    <div class="filter-group">
                        <h4>SSD</h4>
                        <?php $ssds = ['256GB', '512GB', '1TB']; foreach($ssds as $s): ?>
                            <label><input type="checkbox" name="ssd[]" value="<?php echo $s; ?>" <?php if(in_array($s, $filter_ssd)) echo 'checked'; ?>> <?php echo $s; ?></label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array('storage', $enabled_filters)): ?>
                    <div class="filter-group">
                        <h4>Armazenamento Interno</h4>
                        <?php $stgs = ['64GB', '128GB', '256GB', '512GB']; foreach($stgs as $s): ?>
                            <label><input type="checkbox" name="storage[]" value="<?php echo $s; ?>" <?php if(in_array($s, $filter_storage)) echo 'checked'; ?>> <?php echo $s; ?></label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array('tipo_impressao', $enabled_filters)): ?>
                    <div class="filter-group">
                        <h4>Tecnologia</h4>
                        <label><input type="checkbox" name="tipo_imp[]" value="Laser"> Laser</label>
                        <label><input type="checkbox" name="tipo_imp[]" value="Jato de Tinta"> Jato de Tinta</label>
                        <label><input type="checkbox" name="tipo_imp[]" value="Tanque"> Tanque de Tinta</label>
                    </div>
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
                    // Card Lógica
                    $price = get_post_meta( get_the_ID(), '_crns_price', true );
                    $old = get_post_meta( get_the_ID(), '_crns_old_price', true );
                    
                    // Specs Dinâmicas para o Card
                    $specs_keys = crns_get_specs_by_category($current_slug);
                    $specs_html = ''; $c=0;
                    foreach($specs_keys as $k) {
                        if($c>=2) break;
                        $val = get_post_meta(get_the_ID(), $k, true);
                        if($val){ $specs_html .= "<span>$val</span> • "; $c++; }
                    }
                    ?>
                    
                    <div class="offer-card-v2">
                        <div class="card-img">
                             <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                        </div>
                        <div class="card-info">
                            <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="card-specs-mini"><?php echo rtrim($specs_html, ' • '); ?></div>
                            <div class="card-price-block">
                                <span class="card-price">R$ <?php echo $price; ?></span>
                            </div>
                        </div>
                        <div class="card-action">
                             <a href="<?php the_permalink(); ?>" class="btn-buy-v2 btn-outline">Ver Detalhes</a>
                        </div>
                    </div>
                <?php endwhile; 
                the_posts_pagination();
                else : echo '<p style="padding:20px;">Nenhum produto encontrado.</p>'; endif; ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>