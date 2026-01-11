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

/* --- 1. CENTRAL DE ESPECIFICAÇÕES (ATUALIZADA) --- */
function crns_get_all_specs_labels() {
    return [
        // Hardware Comum
        '_crns_cpu'     => 'Processador',
        '_crns_ram'     => 'Memória RAM',
        '_crns_gpu'     => 'Placa de Vídeo',
        '_crns_storage' => 'Armazenamento',
        '_crns_screen'  => 'Tela',
        
        // Celulares (Novos)
        '_crns_camera'  => 'Câmeras',
        '_crns_battery' => 'Bateria',

        // Impressoras
        '_crns_print_tech' => 'Tecnologia de Impressão',
        '_crns_print_res'  => 'Resolução',
        '_crns_print_speed'=> 'Velocidade',
        
        // Genérico
        '_crns_conec'   => 'Conectividade'
    ];
}

// Mapeamento à prova de erros (Slug -> Campos)
function crns_get_specs_by_category( $slug ) {
    // Normaliza para evitar erro se usar singular/plural
    if(strpos($slug, 'notebook') !== false) $slug = 'notebooks';
    if(strpos($slug, 'celular') !== false || strpos($slug, 'smartphone') !== false) $slug = 'smartphones';
    if(strpos($slug, 'impressora') !== false) $slug = 'impressoras';

    $map = [
        'notebooks'   => ['_crns_cpu', '_crns_ram', '_crns_gpu', '_crns_storage', '_crns_screen', '_crns_conec'],
        'smartphones' => ['_crns_cpu', '_crns_ram', '_crns_storage', '_crns_screen', '_crns_camera', '_crns_battery'],
        'impressoras' => ['_crns_print_tech', '_crns_print_res', '_crns_print_speed', '_crns_conec'],
        'perifericos' => ['_crns_conec']
    ];
    return isset($map[$slug]) ? $map[$slug] : []; 
}

/* --- 2. CPT Review + Taxonomias --- */
function crns_register_review_cpt() {
    register_post_type( 'review', array(
        'labels' => array( 'name' => 'Reviews', 'singular_name' => 'Review', 'menu_name' => 'Reviews (Produtos)' ),
        'public' => true, 'has_archive' => true, 'menu_icon' => 'dashicons-cart', 'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ), 'show_in_rest' => true
    ));
    register_taxonomy( 'marca', 'review', array( 'label' => 'Marcas', 'rewrite' => array( 'slug' => 'marca' ), 'hierarchical' => true, 'show_in_rest' => true ));
    register_taxonomy( 'tipo_produto', 'review', array( 'labels' => array( 'name' => 'Tipos de Produto', 'singular_name' => 'Tipo de Produto' ), 'rewrite' => array( 'slug' => 'categoria' ), 'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true ));
}
add_action( 'init', 'crns_register_review_cpt' );

/* --- 3. Meta Boxes (Admin) --- */
function crns_add_product_meta_boxes() { add_meta_box( 'crns_product_details', 'Ficha Técnica', 'crns_product_meta_callback', 'review', 'normal', 'high' ); }
add_action( 'add_meta_boxes', 'crns_add_product_meta_boxes' );

function crns_product_meta_callback( $post ) {
    wp_nonce_field( 'crns_save_product_data', 'crns_product_meta_nonce' );
    $fields = ['_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link'];
    echo '<div style="background:#f0f0f1; padding:15px; margin-bottom:20px; border:1px solid #ccc;"><h4>Dados de Venda</h4><div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">';
    foreach($fields as $f) { 
        echo '<div><label>'.str_replace('_crns_','', $f).'</label><input type="text" name="'.$f.'" value="'.esc_attr(get_post_meta($post->ID, $f, true)).'" style="width:100%"></div>'; 
    }
    echo '</div></div><hr><h4>Especificações (Preencha o que for útil)</h4><div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">';
    foreach(crns_get_all_specs_labels() as $k => $label) {
        echo '<div><label>'.$label.'</label><input type="text" name="'.$k.'" value="'.esc_attr(get_post_meta($post->ID, $k, true)).'" style="width:100%"></div>';
    }
    echo '</div>';
}

function crns_save_product_data( $post_id ) {
    if ( ! isset( $_POST['crns_product_meta_nonce'] ) || ! wp_verify_nonce( $_POST['crns_product_meta_nonce'], 'crns_save_product_data' ) ) return;
    $all = array_merge(['_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link'], array_keys( crns_get_all_specs_labels() ));
    foreach( $all as $field ) { if ( isset( $_POST[$field] ) ) update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) ); }
}
add_action( 'save_post', 'crns_save_product_data' );

/* --- 4. Shortcode --- */
function crns_compare_shortcode( $atts ) { /* ... manter igual ao anterior ou simplificar ... */ return ''; } 
// (Mantive vazio aqui para economizar espaço, pode usar o do passo anterior se quiser o shortcode)

/* --- 5. JSON-LD --- */
function crns_add_review_schema() {
    if ( is_singular( 'review' ) ) {
        global $post;
        $price = get_post_meta( $post->ID, '_crns_price', true );
        if( $price ) {
            echo '<script type="application/ld+json"> { "@context": "https://schema.org/", "@type": "Product", "name": "'.get_the_title().'", "offers": { "@type": "Offer", "price": "'.$price.'", "priceCurrency": "BRL" } } </script>';
        }
    }
}
add_action( 'wp_head', 'crns_add_review_schema' );

/* --- 6. QUERY FILTERS (BACKEND) - CORRIGIDO --- */
function crns_filter_offer_query( $query ) {
    if ( !is_admin() && $query->is_main_query() && is_tax('tipo_produto') ) {
        $meta_query = array('relation' => 'AND');

        // Preço
        if ( !empty($_GET['min_price']) ) {
            $meta_query[] = array( 'key' => '_crns_price', 'value' => array( $_GET['min_price'], $_GET['max_price'] ?: 999999 ), 'type' => 'NUMERIC', 'compare' => 'BETWEEN' );
        }

        // RAM
        if ( !empty($_GET['ram']) ) {
            $mq = array('relation' => 'OR');
            foreach($_GET['ram'] as $v) $mq[] = array('key' => '_crns_ram', 'value' => $v, 'compare' => 'LIKE');
            $meta_query[] = $mq;
        }

        // SSD ou Storage (Celular) - AMBOS buscam no mesmo campo
        $storage_filter = !empty($_GET['ssd']) ? $_GET['ssd'] : (!empty($_GET['storage']) ? $_GET['storage'] : []);
        if ( !empty($storage_filter) ) {
            $mq = array('relation' => 'OR');
            foreach($storage_filter as $v) $mq[] = array('key' => '_crns_storage', 'value' => $v, 'compare' => 'LIKE');
            $meta_query[] = $mq;
        }

        // Tipo Impressão
        if ( !empty($_GET['tipo_imp']) ) {
            $mq = array('relation' => 'OR');
            foreach($_GET['tipo_imp'] as $v) $mq[] = array('key' => '_crns_print_tech', 'value' => $v, 'compare' => 'LIKE');
            $meta_query[] = $mq;
        }

        // Marcas
        if ( !empty($_GET['marca']) ) {
            $query->set('tax_query', array( array( 'taxonomy' => 'marca', 'field' => 'slug', 'terms' => $_GET['marca'] ) ) );
        }

        $query->set('meta_query', $meta_query);
    }
}
add_action( 'pre_get_posts', 'crns_filter_offer_query' );

/* --- 7. CONFIGURAÇÃO VISUAL (FRONTEND) - CORRIGIDO --- */
function crns_get_filters_config( $slug ) {
    // Normalização (aceita celular, smartphone, smartphones...)
    if(strpos($slug, 'notebook') !== false) $slug = 'notebooks';
    if(strpos($slug, 'celular') !== false || strpos($slug, 'smartphone') !== false) $slug = 'smartphones';
    if(strpos($slug, 'impressora') !== false) $slug = 'impressoras';

    $filters = [
        'notebooks'   => ['marca', 'ram', 'ssd'],
        'smartphones' => ['marca', 'ram', 'storage'], // Celular usa "storage" ao invés de SSD
        'impressoras' => ['marca', 'tipo_impressao'],
        'perifericos' => ['marca'],
    ];
    return isset($filters[$slug]) ? $filters[$slug] : ['marca', 'price'];
}