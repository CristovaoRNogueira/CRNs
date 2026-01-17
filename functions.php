<?php

require get_template_directory() . '/inc/customizer.php';

function crns_load_scripts(){
    // Carrega o CSS Principal
    wp_enqueue_style( 'crns-style', get_stylesheet_uri(), array(), filemtime( get_template_directory() . '/style.css' ), 'all' );
    
    // Carrega Fontes Google
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap', array(), null );
    
    // CARREGA OS ÍCONES 
    wp_enqueue_style( 'dashicons' );
    
    // Scripts JS
    wp_enqueue_script( 'dropdown', get_template_directory_uri() . '/js/dropdown.js', array(), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'crns_load_scripts' );


function crns_config(){
    $textdomain = 'crns';
    load_theme_textdomain( $textdomain, get_template_directory() . '/languages/' );
    
    // REGISTRO DOS MENUS (Atualizado)
    register_nav_menus( array( 
        'wp_devs_main_menu'   => 'Main Menu (Antigo)', 
        'wp_devs_footer_menu' => 'Footer Menu',
        'desktop_center'      => 'Menu Principal (Ícones)', // Usado no Desktop e Faixa 2 Mobile
        'drawer_menu'         => 'Menu Gaveta (Lateral)'    // Usado no menu sanduíche
    ) );

    add_theme_support( 'custom-header', array( 'height' => 225, 'width' => 1920 ) );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array( 'width' => 200, 'height' => 110, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ));
    add_theme_support( 'title-tag' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'style-editor.css' );
    add_theme_support( 'wp-block-styles' );
}
add_action( 'after_setup_theme', 'crns_config', 0 );


function crns_register_block_styles(){
    wp_register_style( 'crns-block-style', get_template_directory_uri() . '/block-style.css' );
    register_block_style( 'core/quote', array( 'name' => 'red-quote', 'label' => 'Red Quote', 'is_default' => true, 'style_handle' => 'crns-block-style' ) );
}
add_action( 'init', 'crns_register_block_styles' );

add_action( 'widgets_init', 'crns_sidebars' );
function crns_sidebars(){
    register_sidebar( array( 'name' => 'Blog Sidebar', 'id' => 'sidebar-blog', 'before_widget' => '<div class="widget-wrapper">', 'after_widget' => '</div>', 'before_title' => '<h4 class="widget-title">', 'after_title' => '</h4>' ) );
}

if ( ! function_exists( 'wp_body_open' ) ){ function wp_body_open() { do_action( 'wp_body_open' ); } }

/* --- CPT Review + Taxonomias (ATUALIZADO) --- */
function crns_register_review_cpt() {
    register_post_type( 'review', array(
        'labels' => array( 'name' => 'Reviews', 'singular_name' => 'Review', 'menu_name' => 'Reviews (Produtos)' ),
        'public' => true, 'has_archive' => true, 'menu_icon' => 'dashicons-cart', 'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ), 'show_in_rest' => true
    ));
    
    // Taxonomia: Marca
    register_taxonomy( 'marca', 'review', array( 'label' => 'Marcas', 'rewrite' => array( 'slug' => 'marca' ), 'hierarchical' => true, 'show_in_rest' => true ));
    
    // Taxonomia: Tipo de Produto (Notebook, Smartphone)
    register_taxonomy( 'tipo_produto', 'review', array( 'labels' => array( 'name' => 'Tipos de Produto', 'singular_name' => 'Tipo de Produto' ), 'rewrite' => array( 'slug' => 'categoria' ), 'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true ));

    // NOVO: Taxonomia: Classificação (Gamer, Premium, Custo-Beneficio)
    register_taxonomy( 'classificacao', 'review', array( 
        'labels' => array( 'name' => 'Classificações / Perfis', 'singular_name' => 'Classificação' ), 
        'rewrite' => array( 'slug' => 'perfil' ), 
        'hierarchical' => true, 
        'show_in_rest' => true,
        'show_admin_column' => true 
    ));
}
add_action( 'init', 'crns_register_review_cpt' );

/* --- CONFIGURAÇÃO DE CAMPOS PADRÕES --- */
function crns_get_standard_fields() {
    return [
        'notebooks' => [
            '_crns_cpu' => 'Processador',
            '_crns_ram' => 'Memória RAM',
            '_crns_storage' => 'Armazenamento (SSD/HD)',
            '_crns_gpu' => 'Placa de Vídeo',
            '_crns_screen' => 'Tela',
            '_crns_os' => 'Sistema Operacional'
        ],
        'smartphones' => [
            '_crns_storage' => 'Armazenamento Interno',
            '_crns_ram' => 'Memória RAM',
            '_crns_screen' => 'Tela',
            '_crns_camera_main' => 'Câmera Traseira',
            '_crns_camera_front' => 'Câmera Frontal',
            '_crns_battery' => 'Bateria'
        ],
        'impressoras' => [
            '_crns_print_tech' => 'Tipo de Impressão (Laser/Jato)',
            '_crns_print_color' => 'Cor (Colorida/Preto)',
            '_crns_print_conn' => 'Conectividade (Wifi/USB)',
            '_crns_voltage' => 'Voltagem'
        ],
        'geral' => [
             '_crns_weight' => 'Peso'
        ]
    ];
}

/* --- META BOXES HÍBRIDAS --- */
function crns_add_product_meta_boxes() { add_meta_box( 'crns_product_details', 'Ficha Técnica do Produto', 'crns_product_meta_callback', 'review', 'normal', 'high' ); }
add_action( 'add_meta_boxes', 'crns_add_product_meta_boxes' );

function crns_product_meta_callback( $post ) {
    wp_nonce_field( 'crns_save_product_data', 'crns_product_meta_nonce' );
    
    // 1. DADOS DE VENDA
    $sale_fields = ['_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link'];
    echo '<div style="background:#eef2f7; padding:15px; margin-bottom:20px; border:1px solid #ccd0d4;">
    <h4 style="margin-top:0; border-bottom:1px solid #ccc; padding-bottom:10px;">💰 Dados de Venda</h4>
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">';
    foreach($sale_fields as $f) { 
        echo '<div><label style="font-weight:600">'.str_replace('_crns_','', $f).'</label><input type="text" name="'.$f.'" value="'.esc_attr(get_post_meta($post->ID, $f, true)).'" style="width:100%"></div>'; 
    }
    echo '</div></div>';

    // 2. CAMPOS PREDEFINIDOS
    $all_standards = crns_get_standard_fields();
    echo '<div style="background:#fff; padding:15px; margin-bottom:20px; border:1px solid #ccd0d4;">';
    echo '<h4 style="margin-top:0; border-bottom:1px solid #ccc; padding-bottom:10px;">📋 Campos Padrões (Preencha conforme a categoria)</h4>';
    
    foreach($all_standards as $group => $fields) {
        echo '<h5 style="margin-bottom:5px; color:#0073aa; text-transform:uppercase;">'. $group .'</h5>';
        echo '<div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:15px;">';
        foreach($fields as $key => $label) {
            $val = get_post_meta($post->ID, $key, true);
            echo '<div><label>'.$label.'</label><input type="text" name="'.$key.'" value="'.esc_attr($val).'" style="width:100%"></div>';
        }
        echo '</div>';
    }
    echo '</div>';

    // 3. CAMPOS DINÂMICOS
    $all_meta = get_post_meta($post->ID);
    $dynamic_specs = [];
    foreach($all_meta as $key => $val) {
        if (strpos($key, '_spec_') === 0) {
            $label = crns_format_spec_label($key);
            $dynamic_specs[] = ['key' => $key, 'label' => $label, 'value' => $val[0]];
        }
    }

    echo '<div style="background:#fff; padding:15px; border:1px solid #ccd0d4;">';
    echo '<h4 style="margin-top:0;">✨ Campos Extras (Dinâmicos)</h4>';
    echo '<div id="specs-wrapper">';
    foreach($dynamic_specs as $spec) {
        echo '<div class="spec-row" style="display:flex; gap:10px; margin-bottom:10px;">';
        echo '<input type="text" name="spec_names[]" value="'.esc_attr($spec['label']).'" placeholder="Nome" style="width:40%">';
        echo '<input type="text" name="spec_values[]" value="'.esc_attr($spec['value']).'" placeholder="Valor" style="width:50%">';
        echo '<button type="button" class="button remove-row">X</button>';
        echo '</div>';
    }
    echo '</div>';
    echo '<button type="button" class="button button-secondary" id="add-spec-row">+ Adicionar Campo Extra</button>';
    echo '</div>';
    ?>
    <script>
    jQuery(document).ready(function($){
        $('#add-spec-row').click(function(){
            $('#specs-wrapper').append('<div class="spec-row" style="display:flex; gap:10px; margin-bottom:10px;"><input type="text" name="spec_names[]" placeholder="Nome" style="width:40%"> <input type="text" name="spec_values[]" placeholder="Valor" style="width:50%"> <button type="button" class="button remove-row">X</button></div>');
        });
        $(document).on('click', '.remove-row', function(){ $(this).parent('.spec-row').remove(); });
    });
    </script>
    <?php
}

function crns_save_product_data( $post_id ) {
    if ( ! isset( $_POST['crns_product_meta_nonce'] ) || ! wp_verify_nonce( $_POST['crns_product_meta_nonce'], 'crns_save_product_data' ) ) return;
    
    // Salva Venda
    foreach(['_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link'] as $f) { 
        if(isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_text_field($_POST[$f])); 
    }

    // Salva Padrões
    foreach(crns_get_standard_fields() as $group) {
        foreach($group as $key => $label) {
            if(isset($_POST[$key])) update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    // Salva Dinâmicos
    global $wpdb;
    $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE '_spec_%'", $post_id) );
    if ( isset($_POST['spec_names']) && isset($_POST['spec_values']) ) {
        $names = $_POST['spec_names'];
        $values = $_POST['spec_values'];
        for ( $i = 0; $i < count($names); $i++ ) {
            if ( !empty($names[$i]) && !empty($values[$i]) ) {
                $slug = sanitize_title( $names[$i] );
                $meta_key = '_spec_' . $slug;
                update_post_meta( $post_id, $meta_key, sanitize_text_field( $values[$i] ) );
            }
        }
    }
}
add_action( 'save_post', 'crns_save_product_data' );

/* --- FORMATAR LABEL --- */
function crns_format_spec_label( $meta_key ) {
    $clean = str_replace(['_spec_', '_crns_'], '', $meta_key);
    $clean = str_replace(['_', '-'], ' ', $clean);
    
    $map = [
        'cpu' => 'Processador', 'ram' => 'Memória RAM', 'storage' => 'Armazenamento',
        'screen' => 'Tela', 'os' => 'Sistema Operacional', 'gpu' => 'Placa de Vídeo',
        'camera main' => 'Câmera Traseira', 'camera front' => 'Câmera Frontal',
        'battery' => 'Bateria', 'print tech' => 'Tipo de Impressão',
        'print color' => 'Cor de Impressão', 'print conn' => 'Conectividade',
        'voltage' => 'Voltagem', 'weight' => 'Peso'
    ];
    
    if(array_key_exists($clean, $map)) return $map[$clean];
    return ucwords($clean);
}

/* --- MOTOR DE FILTROS: VERSÃO OTIMIZADA COM CACHE --- */
function crns_get_sidebar_filters( $term_id = 0 ) {
    // 1. Tenta pegar do cache primeiro
    $cache_key = 'crns_filters_' . $term_id;
    $cached_filters = get_transient( $cache_key );

    if ( false !== $cached_filters ) {
        return $cached_filters;
    }

    // Se não tiver cache, processa o banco de dados
    global $wpdb;
    
    $post_ids = [];
    if ( $term_id > 0 ) {
        $post_ids = get_objects_in_term( $term_id, 'tipo_produto' );
    }

    if ( $term_id > 0 && empty($post_ids) ) {
        return [];
    }

    $query = "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_value != ''";
    $ids_string = '';
    
    if ( !empty($post_ids) ) {
        $ids_string = implode(',', array_map('intval', $post_ids));
        $query .= " AND post_id IN ($ids_string)";
    }

    $query .= " AND (meta_key LIKE '_spec_%' OR meta_key LIKE '_crns_%') AND meta_key NOT IN ('_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link', '_edit_lock', '_edit_last', '_thumbnail_id', '_wp_page_template')";

    $keys = $wpdb->get_col($query);
    $filters = [];

    if($keys) {
        foreach($keys as $key) {
            $val_query = "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != ''";
            if ( !empty($post_ids) ) {
                $val_query .= " AND post_id IN ($ids_string)";
            }
            $val_query .= " LIMIT 50";

            $values = $wpdb->get_col( $wpdb->prepare($val_query, $key) );
            
            if(!empty($values)) {
                $label = crns_format_spec_label($key);
                $clean_slug = str_replace(['_spec_', '_crns_'], '', $key);
                $url_param = 'f_' . $clean_slug;
                sort($values, SORT_NATURAL);
                $filters[$url_param] = [
                    'label' => $label,
                    'options' => $values
                ];
            }
        }
    }

    // 2. Salva o resultado no cache por 24 horas (DAY_IN_SECONDS)
    set_transient( $cache_key, $filters, DAY_IN_SECONDS );

    return $filters;
}

// Limpar o cache quando salvar/editar um produto para atualizar filtros novos
add_action( 'save_post', 'crns_clear_filter_cache' );
function crns_clear_filter_cache() {
    global $wpdb;
    // Apaga todos os transients relacionados a filtros
    $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_crns_filters_%'" );
}

/* --- APLICAÇÃO DOS FILTROS NA QUERY (GLOBAL INTELIGENTE - ATUALIZADO) --- */
function crns_filter_offer_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( $query->is_page() ) return;

    if ( is_post_type_archive('review') || is_tax('tipo_produto') || is_tax('marca') || is_tax('classificacao') ) {
        
        $meta_query = $query->get('meta_query');
        if( !is_array($meta_query) ) $meta_query = [];
        $meta_query['relation'] = 'AND';

        // Preço
        if ( !empty($_GET['min_price']) ) {
            $meta_query[] = array( 'key' => '_crns_price', 'value' => array( $_GET['min_price'], $_GET['max_price'] ?: 999999 ), 'type' => 'NUMERIC', 'compare' => 'BETWEEN' );
        }
        
        // Marca
        if ( !empty($_GET['marca']) && !is_tax('marca') ) {
            $tax_query = $query->get('tax_query') ?: [];
            $tax_query[] = array( 'taxonomy' => 'marca', 'field' => 'slug', 'terms' => $_GET['marca'] );
            $query->set('tax_query', $tax_query);
        }

        // NOVO: Classificação (Gamer, Premium, etc)
        if ( !empty($_GET['classificacao']) && !is_tax('classificacao') ) {
            $tax_query = $query->get('tax_query') ?: [];
            $tax_query[] = array( 'taxonomy' => 'classificacao', 'field' => 'slug', 'terms' => $_GET['classificacao'] );
            $query->set('tax_query', $tax_query);
        }

        // Filtros Dinâmicos
        foreach($_GET as $param => $values) {
            if (strpos($param, 'f_') === 0 && !empty($values)) {
                $slug = str_replace('f_', '', $param);
                $mq = array('relation' => 'OR');
                if(is_array($values)) {
                    foreach($values as $v) {
                        $v = urldecode($v);
                        $mq[] = array('key' => '_crns_' . $slug, 'value' => $v, 'compare' => 'LIKE');
                        $mq[] = array('key' => '_spec_' . $slug, 'value' => $v, 'compare' => 'LIKE');
                    }
                } else {
                    $values = urldecode($values);
                    $mq[] = array('key' => '_crns_' . $slug, 'value' => $values, 'compare' => 'LIKE');
                    $mq[] = array('key' => '_spec_' . $slug, 'value' => $values, 'compare' => 'LIKE');
                }
                $meta_query[] = $mq;
            }
        }
        $query->set('meta_query', $meta_query);
    }
}
add_action( 'pre_get_posts', 'crns_filter_offer_query' );

/* --- SEGURANÇA --- */
function crns_remove_native_custom_fields() { remove_meta_box( 'postcustom', 'review', 'normal' ); }
add_action( 'admin_menu', 'crns_remove_native_custom_fields' );

/* --- SCRIPT DROPDOWN --- */
/* --- SCRIPT FILTROS (Acordeão + Mobile Toggle) --- */
function crns_filter_accordion_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Lógica do Acordeão (Títulos H4)
        const triggers = document.querySelectorAll('.filter-group h4');
        triggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                this.parentElement.classList.toggle('active');
            });
        });

        // 2. Lógica do Botão Mobile (Expandir/Recolher)
        const mobileBtn = document.getElementById('mobile-filter-toggle');
        const secFilters = document.getElementById('secondary-filters');

        if(mobileBtn && secFilters) {
            mobileBtn.addEventListener('click', function() {
                secFilters.classList.toggle('open');
                
                // (Opcional) Troca o texto do botão
                if(secFilters.classList.contains('open')) {
                    mobileBtn.innerHTML = '<span class="dashicons dashicons-arrow-up-alt2"></span> Ocultar Filtros';
                    mobileBtn.style.background = '#f9f9f9';
                    mobileBtn.style.color = '#555';
                    mobileBtn.style.borderColor = '#ccc';
                } else {
                    mobileBtn.innerHTML = '<span class="dashicons dashicons-filter"></span> Filtrar por Preço, Marca e Specs';
                    mobileBtn.style.background = '#fff';
                    mobileBtn.style.color = 'var(--primary)';
                    mobileBtn.style.borderColor = 'var(--primary)'; // Se não funcionar var, use a cor hex #ff5500
                }
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'crns_filter_accordion_script');

// Schema JSON-LD Otimizado para Reviews
function crns_add_review_schema() {
    if ( is_singular( 'review' ) ) {
        global $post; 
        $price = get_post_meta( $post->ID, '_crns_price', true );
        $rating = get_post_meta( $post->ID, '_crns_rating', true ); // Sua nota (ex: 9.5)
        $img_url = get_the_post_thumbnail_url($post->ID, 'full');
        $excerpt = wp_strip_all_tags(get_the_excerpt());

        if( $price ) {
            // Converte nota 0-10 para escala 0-5 se necessário, ou mantém 0-10
            // O Google prefere 1-5, vamos adaptar:
            $rating_value = $rating ? $rating : '0';
            
            echo '<script type="application/ld+json">
            {
              "@context": "https://schema.org/",
              "@type": "Product",
              "name": "'. get_the_title() .'",
              "image": "'. $img_url .'",
              "description": "'. $excerpt .'",
              "offers": {
                "@type": "Offer",
                "url": "'. get_the_permalink() .'",
                "priceCurrency": "BRL",
                "price": "'. $price .'",
                "availability": "https://schema.org/InStock"
              },
              "review": {
                "@type": "Review",
                "reviewRating": {
                  "@type": "Rating",
                  "ratingValue": "'. $rating_value .'",
                  "bestRating": "10",
                  "worstRating": "1"
                },
                "author": {
                  "@type": "Person",
                  "name": "Equipe '. get_bloginfo('name') .'"
                }
              }
            }
            </script>';
        }
    }
}
add_action( 'wp_head', 'crns_add_review_schema' );


function crns_compare_shortcode($atts) { return ''; } add_shortcode('comparar', 'crns_compare_shortcode');

/* --- BUSCAR CLASSIFICAÇÕES POR CATEGORIA (INTELIGENTE) --- */
function crns_get_valid_classifications( $cat_slug = '' ) {
    // Se não tiver categoria selecionada, retorna todas as classificações que têm posts
    if ( empty($cat_slug) ) {
        return get_terms(['taxonomy' => 'classificacao', 'hide_empty' => true]);
    }

    global $wpdb;

    // Query SQL Avançada:
    // 1. Busca termos da taxonomia 'classificacao' (t_class)
    // 2. Que estejam associados a posts (tr_class)
    // 3. Onde esses mesmos posts TAMBÉM estejam associados (tr_cat)
    // 4. À categoria selecionada 'tipo_produto' (t_cat)
    
    $query = "
        SELECT DISTINCT t_class.*
        FROM {$wpdb->terms} t_class
        INNER JOIN {$wpdb->term_taxonomy} tt_class ON t_class.term_id = tt_class.term_id
        INNER JOIN {$wpdb->term_relationships} tr_class ON tt_class.term_taxonomy_id = tr_class.term_taxonomy_id
        INNER JOIN {$wpdb->posts} p ON tr_class.object_id = p.ID
        INNER JOIN {$wpdb->term_relationships} tr_cat ON p.ID = tr_cat.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt_cat ON tr_cat.term_taxonomy_id = tt_cat.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t_cat ON tt_cat.term_id = t_cat.term_id
        WHERE tt_class.taxonomy = 'classificacao'
        AND t_cat.slug = %s
        AND p.post_status = 'publish'
    ";

    $results = $wpdb->get_results( $wpdb->prepare($query, $cat_slug) );
    
    return $results;
}

/* --- SCRIPT DO MENU MOBILE --- */
function crns_mobile_menu_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.menu-toggle');
        const nav = document.querySelector('.main-navigation');

        if(toggleBtn && nav) {
            toggleBtn.addEventListener('click', function() {
                nav.classList.toggle('toggled');
                
                // Animação do ícone hambúrguer (X)
                const expanded = toggleBtn.getAttribute('aria-expanded') === 'true' || false;
                toggleBtn.setAttribute('aria-expanded', !expanded);
                toggleBtn.classList.toggle('active');
            });
        }
    });
    </script>
    <style>
        /* Estilo extra para transformar o hambúrguer em X */
        .menu-toggle.active .bar:nth-child(2) { opacity: 0; }
        .menu-toggle.active .bar:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .menu-toggle.active .bar:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }
    </style>
    <?php
}
add_action('wp_footer', 'crns_mobile_menu_script');

