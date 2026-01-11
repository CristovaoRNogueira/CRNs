<?php get_header(); ?>

<div id="primary" class="content-area-review">
    <main id="main" class="site-main">
        <?php while( have_posts() ): the_post(); 
            $price = get_post_meta( get_the_ID(), '_crns_price', true );
            $link = get_post_meta( get_the_ID(), '_crns_affiliate_link', true );
            $rating = get_post_meta( get_the_ID(), '_crns_rating', true );
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('review-container'); ?>>
                <header class="review-header">
                    <div class="container">
                        <h1><?php the_title(); ?></h1>
                    </div>
                </header>

                <div class="container review-grid">
                    <div class="review-main-card">
                        <div class="product-image-box">
                            <?php the_post_thumbnail('large'); ?>
                            <?php if($rating): ?><div class="rating-badge"><?php echo $rating; ?></div><?php endif; ?>
                        </div>
                        <div class="cta-box">
                            <div class="price-tag"><strong>R$ <?php echo $price; ?></strong></div>
                            <?php if($link): ?><a href="<?php echo $link; ?>" class="btn-affiliate pulse">VER NA LOJA</a><?php endif; ?>
                        </div>
                    </div>

                    <div class="specs-box">
                        <h3>Ficha Técnica</h3>
                        <ul>
                            <?php 
                            $all_meta = get_post_meta(get_the_ID());
                            $found = false;
                            
                            // 1. CAMPOS PADRÕES (Mostra primeiro, com labels bonitos)
                            $standard_keys = [
                                '_crns_os', '_crns_cpu', '_crns_ram', '_crns_storage', 
                                '_crns_screen', '_crns_gpu', '_crns_camera_main', 
                                '_crns_camera_front', '_crns_battery', '_crns_print_tech',
                                '_crns_print_color', '_crns_print_conn', '_crns_voltage',
                                '_crns_weight'
                            ];

                            foreach($standard_keys as $key) {
                                if(isset($all_meta[$key][0]) && $all_meta[$key][0] !== '') {
                                    // Formata usando a função global ou usa o nome da chave se falhar
                                    $label = function_exists('crns_format_spec_label') ? crns_format_spec_label($key) : $key;
                                    echo "<li><strong>{$label}:</strong> <span>{$all_meta[$key][0]}</span></li>";
                                    $found = true;
                                }
                            }

                            // 2. CAMPOS EXTRAS (Dinâmicos)
                            // Agora só vai aparecer o que você REALMENTE cadastrou no botão "Adicionar Campo Extra"
                            foreach($all_meta as $key => $values) {
                                if (strpos($key, '_spec_') === 0 && $values[0] !== '') {
                                    $label = function_exists('crns_format_spec_label') ? crns_format_spec_label($key) : ucwords(str_replace('_spec_', '', $key));
                                    echo "<li><strong>{$label}:</strong> <span>{$values[0]}</span></li>";
                                    $found = true;
                                }
                            }

                            if(!$found) echo "<li style='color:#999'>Especificações não cadastradas.</li>";
                            ?>
                        </ul>
                        <p class="specs-disclaimer">*Especificações fornecidas pelo fabricante.</p>
                    </div>
                </div>

                <div class="container review-content"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </main>
</div>
<?php get_footer(); ?>