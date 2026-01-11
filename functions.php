<?php

require get_template_directory() . '/inc/customizer.php';

function crns_load_scripts(){
    wp_enqueue_style( 'crns-style', get_stylesheet_uri(), array(), filemtime( get_template_directory() . '/style.css' ), 'all' );
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap', array(), null );
    wp_enqueue_script( 'dropdown', get_template_directory_uri() . '/js/dropdown.js', array(), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'crns_load_scripts' );

function crns_config(){
    $textdomain = 'crns';
    load_theme_textdomain( $textdomain, get_template_directory() . '/languages/' );
    register_nav_menus( array( 'wp_devs_main_menu' => 'Main Menu', 'wp_devs_footer_menu' => 'Footer Menu' ) );
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

/* --- CPT Review + Taxonomias --- */
function crns_register_review_cpt() {
    register_post_type( 'review', array(
        'labels' => array( 'name' => 'Reviews', 'singular_name' => 'Review', 'menu_name' => 'Reviews (Produtos)' ),
        'public' => true, 'has_archive' => true, 'menu_icon' => 'dashicons-cart', 'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ), 'show_in_rest' => true
    ));
    register_taxonomy( 'marca', 'review', array( 'label' => 'Marcas', 'rewrite' => array( 'slug' => 'marca' ), 'hierarchical' => true, 'show_in_rest' => true ));
    register_taxonomy( 'tipo_produto', 'review', array( 'labels' => array( 'name' => 'Tipos de Produto', 'singular_name' => 'Tipo de Produto' ), 'rewrite' => array( 'slug' => 'categoria' ), 'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true ));
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

/* --- META BOXES HÍBRIDAS (Fixos + Dinâmicos) --- */
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

    // 2. CAMPOS PREDEFINIDOS (Agrupados)
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

    // 3. CAMPOS DINÂMICOS (Repetidor)
    // Recupera metas que começam com "_spec_"
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
    echo '<p style="font-size:12px; color:#666;">Use para coisas específicas (ex: "Resistência à Água", "RGB", "DPI").</p>';
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
            $('#specs-wrapper').append('<div class="spec-row" style="display:flex; gap:10px; margin-bottom:10px;"><input type="text" name="spec_names[]" placeholder="Nome (ex: Cor)" style="width:40%"> <input type="text" name="spec_values[]" placeholder="Valor" style="width:50%"> <button type="button" class="button remove-row">X</button></div>');
        });
        $(document).on('click', '.remove-row', function(){ $(this).parent('.spec-row').remove(); });
    });
    </script>
    <?php
}

function crns_save_product_data( $post_id ) {
    if ( ! isset( $_POST['crns_product_meta_nonce'] ) || ! wp_verify_nonce( $_POST['crns_product_meta_nonce'], 'crns_save_product_data' ) ) return;
    
    // 1. Salva Dados de Venda
    $sales = ['_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link'];
    foreach($sales as $f) { if(isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_text_field($_POST[$f])); }

    // 2. Salva Campos Padrões
    $standards = crns_get_standard_fields();
    foreach($standards as $group) {
        foreach($group as $key => $label) {
            if(isset($_POST[$key])) update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    // 3. Salva Campos Dinâmicos
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

/* --- FUNÇÃO CORRETORA DE LABEL --- */
function crns_format_spec_label( $meta_key ) {
    // Remove prefixos conhecidos
    $clean = str_replace(['_spec_', '_crns_'], '', $meta_key);
    // Remove sufixos técnicos se houver (ex: camera_main -> Camera Main)
    $clean = str_replace(['_', '-'], ' ', $clean);
    // Tradução manual de slugs comuns para ficar bonito no filtro
    $map = [
        'cpu' => 'Processador', 'ram' => 'Memória RAM', 'storage' => 'Armazenamento',
        'screen' => 'Tela', 'os' => 'Sistema Operacional', 'gpu' => 'Placa de Vídeo',
        'camera main' => 'Câmera Traseira', 'camera front' => 'Câmera Frontal',
        'battery' => 'Bateria', 'print tech' => 'Tipo de Impressão',
        'print color' => 'Cor de Impressão', 'print conn' => 'Conectividade',
        'voltage' => 'Voltagem'
    ];
    
    if(array_key_exists($clean, $map)) return $map[$clean];
    
    return ucwords($clean);
}

/* --- MOTOR DE FILTROS: VERSÃO ROBUSTA --- */
function crns_get_sidebar_filters( $term_id = 0 ) {
    global $wpdb;
    
    // 1. Se estamos numa categoria, pegamos os IDs dos produtos dela
    $post_ids = [];
    if ( $term_id > 0 ) {
        // Pega IDs dos posts que estão na taxonomia 'tipo_produto' com o ID atual
        $post_ids = get_objects_in_term( $term_id, 'tipo_produto' );
    }

    // Se não houver produtos na categoria, não exibe filtros dinâmicos
    if ( $term_id > 0 && empty($post_ids) ) {
        return [];
    }

    // 2. Monta a Query para buscar Meta Keys usadas nesses produtos
    $query = "
        SELECT DISTINCT meta_key 
        FROM {$wpdb->postmeta} 
        WHERE meta_value != ''
    ";

    // Filtra pelos IDs encontrados (se houver categoria definida)
    if ( !empty($post_ids) ) {
        $ids_string = implode(',', array_map('intval', $post_ids));
        $query .= " AND post_id IN ($ids_string)";
    }

    // Filtra apenas chaves que sejam specs (_crns_ ou _spec_)
    // E exclui os campos de controle (preço, rating, etc)
    $query .= "
        AND (meta_key LIKE '_spec_%' OR meta_key LIKE '_crns_%')
        AND meta_key NOT IN ('_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link', '_edit_lock', '_edit_last', '_thumbnail_id', '_wp_page_template')
    ";

    $keys = $wpdb->get_col($query);
    $filters = [];

    if($keys) {
        foreach($keys as $key) {
            // 3. Para cada chave encontrada, busca os valores únicos (Opções do Filtro)
            $val_query = "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != ''";
            
            // Reforça o filtro por categoria também nos valores
            if ( !empty($post_ids) ) {
                $val_query .= " AND post_id IN ($ids_string)";
            }
            
            $val_query .= " LIMIT 50"; // Limite de segurança

            $values = $wpdb->get_col( $wpdb->prepare($val_query, $key) );
            
            // Só adiciona o filtro se houver valores
            if(!empty($values)) {
                $label = crns_format_spec_label($key);
                
                // Gera o parâmetro de URL (remove _spec_ ou _crns_)
                // Ex: _crns_ram -> f_ram
                $clean_slug = str_replace(['_spec_', '_crns_'], '', $key);
                $url_param = 'f_' . $clean_slug;
                
                // Ordena valores (ex: 4GB, 8GB...)
                sort($values, SORT_NATURAL);

                $filters[$url_param] = [
                    'label' => $label,
                    'options' => $values
                ];
            }
        }
    }
    return $filters;
}

/* --- APLICAÇÃO DOS FILTROS NA QUERY (GLOBAL INTELIGENTE) --- */
function crns_filter_offer_query( $query ) {
    // 1. Regras de Segurança:
    // - Não roda no Admin
    // - Só roda na Query Principal
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // 2. REGRA ANTI-404 (A Mágica):
    // Se for uma Página Estática (como a página 'Ofertas'), NÃO aplicamos o filtro na query principal.
    // Isso impede que o WordPress tente filtrar a PÁGINA em si e retorne 404.
    // A página 'Ofertas' fará sua própria filtragem interna (WP_Query customizada).
    if ( $query->is_page() ) {
        return;
    }

    // 3. Onde aplicar os filtros?
    // - Arquivo do Post Type 'review' (se tiver)
    // - Taxonomia 'tipo_produto' (Categorias)
    // - Taxonomia 'marca'
    if ( is_post_type_archive('review') || is_tax('tipo_produto') || is_tax('marca') ) {
        
        $meta_query = $query->get('meta_query');
        if( !is_array($meta_query) ) $meta_query = [];
        $meta_query['relation'] = 'AND';

        // Preço
        if ( !empty($_GET['min_price']) ) {
            $meta_query[] = array( 'key' => '_crns_price', 'value' => array( $_GET['min_price'], $_GET['max_price'] ?: 999999 ), 'type' => 'NUMERIC', 'compare' => 'BETWEEN' );
        }
        
        // Marca (apenas se não estivermos já na página da marca)
        if ( !empty($_GET['marca']) && !is_tax('marca') ) {
            $tax_query = array( array( 'taxonomy' => 'marca', 'field' => 'slug', 'terms' => $_GET['marca'] ) );
            $query->set('tax_query', $tax_query);
        }

        // Filtros Dinâmicos Híbridos
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

// Schema e Shortcode
function crns_add_review_schema() {
    if ( is_singular( 'review' ) ) {
        global $post; $price = get_post_meta( $post->ID, '_crns_price', true );
        if( $price ) echo '<script type="application/ld+json"> { "@context": "https://schema.org/", "@type": "Product", "name": "'.get_the_title().'", "offers": { "@type": "Offer", "price": "'.$price.'", "priceCurrency": "BRL" } } </script>';
    }
}
add_action( 'wp_head', 'crns_add_review_schema' );
function crns_compare_shortcode($atts) { return ''; } add_shortcode('comparar', 'crns_compare_shortcode');






