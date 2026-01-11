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

    register_nav_menus(
        array(
            'wp_devs_main_menu' => esc_html__( 'Main Menu', 'crns' ),
            'wp_devs_footer_menu' => esc_html__( 'Footer Menu', 'crns' )
        )
    );

    $args = array(
        'height'    => 225,
        'width'     => 1920
    );
    add_theme_support( 'custom-header', $args );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'width' => 200,
        'height'    => 110,
        'flex-height'   => true,
        'flex-width'    => true
    ) );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ));
    add_theme_support( 'title-tag' );

    //add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'style-editor.css' );
    add_theme_support( 'wp-block-styles' );
    // add_theme_support( 'editor-color-palette', array(
    //     array(
    //         'name'  => __( 'Primary', 'crns' ),
    //         'slug'  => 'primary',
    //         'color' => '#001E32'
    //     ),
    //     array(
    //         'name'  => __( 'Secondary', 'crns' ),
    //         'slug'  => 'secondary',
    //         'color' => '#CFAF07'
    //     )
    // ) );
    // add_theme_support( 'disable-custom-colors' );
}
add_action( 'after_setup_theme', 'crns_config', 0 );

function crns_register_block_styles(){
    wp_register_style( 'crns-block-style', get_template_directory_uri() . '/block-style.css' );
    register_block_style(
        'core/quote',
        array(
            'name'  => 'red-quote',
            'label' => 'Red Quote',
            'is_default'    => true,
            //'inline_style'  => '.wp-block-quote.is-style-red-quote { border-left: 7px solid #ff0000; background: #f9f3f3; padding: 10px 20px; }',
            'style_handle' => 'crns-block-style'
        )
    );
}
add_action( 'init', 'crns_register_block_styles' );

add_action( 'widgets_init', 'crns_sidebars' );
function crns_sidebars(){
    register_sidebar(
        array(
            'name'  => esc_html__( 'Blog Sidebar', 'crns' ),
            'id'    => 'sidebar-blog',
            'description'   => esc_html__( 'This is the Blog Sidebar. You can add your widgets here.', 'crns' ),
            'before_widget' => '<div class="widget-wrapper">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>'
        )
    );
    register_sidebar(
        array(
            'name'  => esc_html__( 'Service 1', 'crns' ),
            'id'    => 'services-1',
            'description'   => esc_html__( 'First Service Area', 'crns' ),
            'before_widget' => '<div class="widget-wrapper">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>'
        )
    );
    register_sidebar(
        array(
            'name'  => esc_html__( 'Service 2', 'crns' ),
            'id'    => 'services-2',
            'description'   => esc_html__( 'Second Service Area', 'crns' ),
            'before_widget' => '<div class="widget-wrapper">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>'
        )
    );
    register_sidebar(
        array(
            'name'  => esc_html__( 'Service 3', 'crns' ),
            'id'    => 'services-3',
            'description'   => esc_html__( 'Third Service Area', 'crns' ),
            'before_widget' => '<div class="widget-wrapper">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>'
        )
    );
}

if ( ! function_exists( 'wp_body_open' ) ){
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}
/* --- 1. Custom Post Type: Review --- */
function crns_register_review_cpt() {
    $labels = array(
        'name' => 'Reviews',
        'singular_name' => 'Review',
        'menu_name' => 'Reviews (Produtos)',
        'add_new' => 'Adicionar Produto',
        'all_items' => 'Todos os Produtos'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-cart',
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'show_in_rest' => true, // Habilita Gutenberg
    );
    register_post_type( 'review', $args );
}
add_action( 'init', 'crns_register_review_cpt' );

/* --- 2. Campos Personalizados (Meta Boxes) --- */
function crns_add_product_meta_boxes() {
    add_meta_box( 'crns_product_details', 'Ficha Técnica & Afiliado', 'crns_product_meta_callback', 'review', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'crns_add_product_meta_boxes' );

function crns_product_meta_callback( $post ) {
    wp_nonce_field( 'crns_save_product_data', 'crns_product_meta_nonce' );
    
    $fields = [
        '_crns_price' => 'Preço Atual (R$)',
        '_crns_old_price' => 'Preço Original (De:)',
        '_crns_rating' => 'Nota (0-10)',
        '_crns_affiliate_link' => 'Link de Afiliado (URL)',
        '_crns_cpu' => 'Processador (CPU)',
        '_crns_ram' => 'Memória RAM',
        '_crns_gpu' => 'Placa de Vídeo (GPU)',
        '_crns_storage' => 'Armazenamento',
        '_crns_screen' => 'Tela'
    ];

    echo '<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">';
    foreach($fields as $key => $label) {
        $value = get_post_meta( $post->ID, $key, true );
        $type = (strpos($key, 'price') !== false || strpos($key, 'rating') !== false) ? 'number' : 'text';
        $step = ($type === 'number') ? 'step="0.01"' : '';
        
        echo "<div><label><strong>$label</strong></label><br>";
        echo "<input type='$type' name='$key' value='" . esc_attr($value) . "' $step style='width:100%; padding:5px;'></div>";
    }
    echo '</div>';
}

function crns_save_product_data( $post_id ) {
    if ( ! isset( $_POST['crns_product_meta_nonce'] ) || ! wp_verify_nonce( $_POST['crns_product_meta_nonce'], 'crns_save_product_data' ) ) return;
    
    $keys = ['_crns_price', '_crns_old_price', '_crns_rating', '_crns_affiliate_link', '_crns_cpu', '_crns_ram', '_crns_gpu', '_crns_storage', '_crns_screen'];
    
    foreach( $keys as $key ) {
        if ( isset( $_POST[$key] ) ) update_post_meta( $post_id, $key, sanitize_text_field( $_POST[$key] ) );
    }
}
add_action( 'save_post', 'crns_save_product_data' );

/* --- 3. Shortcode Comparador [comparar ids="1,2"] --- */
function crns_compare_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'ids' => '' ), $atts );
    if ( ! $atts['ids'] ) return '';

    $post_ids = explode( ',', $atts['ids'] );
    $output = '<div class="table-responsive"><table class="crns-compare-table"><thead><tr><th>Especificação</th>';

    foreach ( $post_ids as $id ) {
        $id = trim($id);
        $thumb = get_the_post_thumbnail_url($id, 'thumbnail');
        $output .= '<th>' . ($thumb ? "<img src='$thumb' style='height:80px; display:block; margin:0 auto;'>" : '') . '<a href="'.get_permalink($id).'">' . get_the_title($id) . '</a></th>';
    }
    $output .= '</tr></thead><tbody>';

    $specs = [
        '_crns_price' => 'Preço', '_crns_rating' => 'Nota', '_crns_cpu' => 'CPU', 
        '_crns_gpu' => 'GPU', '_crns_ram' => 'RAM', '_crns_storage' => 'Armazenamento', '_crns_screen' => 'Tela'
    ];

    foreach ( $specs as $key => $label ) {
        $output .= '<tr><td class="feature-label"><strong>' . $label . '</strong></td>';
        foreach ( $post_ids as $id ) {
            $val = get_post_meta( trim($id), $key, true );
            if($key == '_crns_price') $val = 'R$ ' . number_format((float)$val, 2, ',', '.');
            $output .= '<td>' . ($val ? $val : '-') . '</td>';
        }
        $output .= '</tr>';
    }
    $output .= '</tbody></table></div>';
    return $output;
}
add_shortcode( 'comparar', 'crns_compare_shortcode' );

/* --- 4. Schema JSON-LD (SEO) --- */
function crns_add_review_schema() {
    if ( is_singular( 'review' ) ) {
        global $post;
        $price = get_post_meta( $post->ID, '_crns_price', true );
        $rating = get_post_meta( $post->ID, '_crns_rating', true );
        if( $rating && $price ) {
            echo '<script type="application/ld+json">
            {
              "@context": "https://schema.org/",
              "@type": "Product",
              "name": "' . get_the_title() . '",
              "image": "' . get_the_post_thumbnail_url($post->ID, 'full') . '",
              "description": "' . esc_js( get_the_excerpt() ) . '",
              "review": {
                "@type": "Review",
                "reviewRating": { "@type": "Rating", "ratingValue": "' . $rating . '", "bestRating": "10" },
                "author": { "@type": "Person", "name": "Editor" }
              },
              "offers": {
                "@type": "Offer",
                "priceCurrency": "BRL",
                "price": "' . $price . '",
                "availability": "https://schema.org/InStock"
              }
            }
            </script>';
        }
    }
}
add_action( 'wp_head', 'crns_add_review_schema' );

/* --- Fim do arquivo functions.php --- */

