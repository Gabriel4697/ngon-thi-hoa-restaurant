<?php
/**
 * Ngon Thi Hoa Theme Functions
 * Brand: Brown #5e4743, Gold #ffc952
 * Languages: VI, EN, ZH (Simplified), KO
 * Fonts: UTM Thu Phap Thien An (headings) + Courier Prime (body)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NTH_THEME_VERSION', '2.0.0' );

// ── Theme Support ─────────────────────────────────────────────────────────────
function nth_theme_setup() {
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );

    // Thumbnail sizes
    add_image_size( 'nth-hero',      1920, 900, true );
    add_image_size( 'nth-menu-card', 600,  450, true );
    add_image_size( 'nth-blog-card', 800,  500, true );
    add_image_size( 'nth-gallery',   800,  600, true );
    add_image_size( 'nth-thumb',     400,  300, true );

    // Navigation menus
    register_nav_menus( [
        'primary'   => __( 'Menu chính', 'ngonthihoa' ),
        'footer'    => __( 'Menu footer', 'ngonthihoa' ),
        'mobile'    => __( 'Menu mobile', 'ngonthihoa' ),
    ] );

    // i18n
    load_theme_textdomain( 'ngonthihoa', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'nth_theme_setup' );

// ── Content Width ─────────────────────────────────────────────────────────────
function nth_content_width() {
    $GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'nth_content_width', 0 );

// ── Enqueue Scripts & Styles ──────────────────────────────────────────────────
function nth_enqueue_assets() {
    $v = NTH_THEME_VERSION;

    // Google Fonts
    wp_enqueue_style(
        'nth-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..700&family=Courier+Prime:wght@400;700&display=swap',
        [],
        null
    );

    // UTM Thu Phap Thien An — served from theme /assets/fonts/
    wp_add_inline_style( 'nth-main', '
        @font-face {
            font-family: "UTM Thu Phap Thien An";
            src: url("' . esc_url( get_template_directory_uri() . '/assets/fonts/UTM-ThuPhapThienAn.ttf' ) . '") format("truetype");
            font-weight: normal; font-style: normal; font-display: swap;
        }
    ' );

    // Main stylesheet
    wp_enqueue_style( 'nth-main', get_stylesheet_uri(), [ 'nth-google-fonts' ], $v );

    // jQuery (WP bundles it)
    wp_enqueue_script( 'jquery' );

    // Theme JS
    wp_enqueue_script( 'nth-theme', get_template_directory_uri() . '/assets/js/theme.js', [ 'jquery' ], $v, true );

    wp_localize_script( 'nth-theme', 'nth_theme', [
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( 'nth_nonce' ),
        'lang'       => nth_get_current_lang(),
        'assets_url' => get_template_directory_uri() . '/assets/',
    ] );

    // reCAPTCHA (if enabled)
    $recaptcha_key = get_option( 'nth_recaptcha_site_key' );
    if ( $recaptcha_key && get_option( 'nth_security_recaptcha' ) ) {
        wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_key ), [], null, true );
    }
}
add_action( 'wp_enqueue_scripts', 'nth_enqueue_assets' );

// ── Widgets ───────────────────────────────────────────────────────────────────
function nth_widgets_init() {
    $args = [
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ];
    register_sidebar( array_merge( $args, [ 'id' => 'sidebar-1', 'name' => __( 'Sidebar', 'ngonthihoa' ), 'description' => __( 'Sidebar widgets', 'ngonthihoa' ) ] ) );
    register_sidebar( array_merge( $args, [ 'id' => 'footer-1', 'name' => __( 'Footer 1', 'ngonthihoa' ) ] ) );
    register_sidebar( array_merge( $args, [ 'id' => 'footer-2', 'name' => __( 'Footer 2', 'ngonthihoa' ) ] ) );
    register_sidebar( array_merge( $args, [ 'id' => 'footer-3', 'name' => __( 'Footer 3', 'ngonthihoa' ) ] ) );
}
add_action( 'widgets_init', 'nth_widgets_init' );

// ── Language helpers ──────────────────────────────────────────────────────────
function nth_get_current_lang() {
    if ( defined( 'ICL_LANGUAGE_CODE' ) ) return ICL_LANGUAGE_CODE;
    if ( function_exists( 'pll_current_language' ) ) return pll_current_language();
    if ( isset( $_COOKIE['nth_language'] ) ) return sanitize_key( $_COOKIE['nth_language'] );
    return get_option( 'nth_default_language', 'vi' );
}

function nth_t( $key, $fallback = '' ) {
    static $strings = null;
    if ( $strings === null ) {
        $strings = require get_template_directory() . '/inc/translations.php';
    }
    $lang = nth_get_current_lang();
    return $strings[ $lang ][ $key ] ?? ( $strings['vi'][ $key ] ?? $fallback );
}

// ── Custom excerpt ────────────────────────────────────────────────────────────
function nth_excerpt_length() { return 25; }
add_filter( 'excerpt_length', 'nth_excerpt_length' );
function nth_excerpt_more() { return '…'; }
add_filter( 'excerpt_more', 'nth_excerpt_more' );

// ── Performance: remove unnecessary head clutter ──────────────────────────────
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );

// Disable XML-RPC if not needed
add_filter( 'xmlrpc_enabled', '__return_false' );

// ── Caching helpers ───────────────────────────────────────────────────────────
function nth_get_menu_items_cached( $group_slug, $per_page = -1 ) {
    $cache_key = 'nth_menu_' . $group_slug . '_' . $per_page;
    $cached    = wp_cache_get( $cache_key, 'nth_menus' );
    if ( false !== $cached ) return $cached;

    $query = new WP_Query( [
        'post_type'      => 'menu_item',
        'posts_per_page' => $per_page,
        'post_status'    => 'publish',
        'meta_key'       => '_nth_sort_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'meta_query'     => $group_slug ? [ [ 'key' => '_nth_menu_group', 'value' => $group_slug ] ] : [],
    ] );

    wp_cache_set( $cache_key, $query->posts, 'nth_menus', HOUR_IN_SECONDS );
    return $query->posts;
}

// Clear menu cache when a menu_item is saved
add_action( 'save_post_menu_item', function() { wp_cache_flush_group( 'nth_menus' ); } );
add_action( 'delete_post', function( $post_id ) {
    if ( get_post_type( $post_id ) === 'menu_item' ) wp_cache_flush_group( 'nth_menus' );
} );

// ── Pagination ────────────────────────────────────────────────────────────────
function nth_pagination() {
    global $wp_query;
    echo paginate_links( [
        'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
        'format'    => '?paged=%#%',
        'current'   => max( 1, get_query_var( 'paged' ) ),
        'total'     => $wp_query->max_num_pages,
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
    ] );
}

// ── Breadcrumb ────────────────────────────────────────────────────────────────
function nth_breadcrumb() {
    echo '<nav class="nth-breadcrumb" aria-label="breadcrumb"><ol>';
    echo '<li><a href="' . home_url() . '">' . nth_t( 'nav_home', 'Trang chủ' ) . '</a></li>';
    if ( is_singular() ) {
        echo '<li>' . esc_html( get_the_title() ) . '</li>';
    } elseif ( is_archive() ) {
        echo '<li>' . esc_html( get_the_archive_title() ) . '</li>';
    }
    echo '</ol></nav>';
}

// ── SEO: Custom title tag ─────────────────────────────────────────────────────
add_filter( 'pre_get_document_title', function( $title ) {
    if ( is_front_page() ) {
        return get_option( 'nth_restaurant_name', 'Ngon Thị Hoa' ) . ' - Nhà hàng ẩm thực Việt Nam tại Đà Nẵng';
    }
    return $title;
} );

// ── Security: limit login attempts message ────────────────────────────────────
add_filter( 'login_errors', function() {
    return __( 'Thông tin đăng nhập không chính xác.', 'ngonthihoa' );
} );

// ── REST API: expose theme options endpoint ───────────────────────────────────
add_action( 'rest_api_init', function() {
    register_rest_route( 'ngonthihoa/v1', '/settings', [
        'methods'             => 'GET',
        'callback'            => function() {
            return [
                'name'          => get_option( 'nth_restaurant_name' ),
                'phone'         => get_option( 'nth_restaurant_phone' ),
                'phone2'        => get_option( 'nth_restaurant_phone2' ),
                'phone3'        => get_option( 'nth_restaurant_phone3' ),
                'email'         => get_option( 'nth_restaurant_email' ),
                'address'       => get_option( 'nth_restaurant_address' ),
                'hours'         => get_option( 'nth_restaurant_hours' ),
                'facebook_url'  => get_option( 'nth_facebook_url' ),
                'tripadvisor_url' => get_option( 'nth_tripadvisor_url' ),
                'maps_url'      => get_option( 'nth_google_maps_url' ),
                'default_lang'  => get_option( 'nth_default_language', 'vi' ),
            ];
        },
        'permission_callback' => '__return_true',
    ] );
} );

// ── Include additional helpers ────────────────────────────────────────────────
if ( file_exists( get_template_directory() . '/inc/template-tags.php' ) ) {
    require get_template_directory() . '/inc/template-tags.php';
}
if ( file_exists( get_template_directory() . '/inc/customizer.php' ) ) {
    require get_template_directory() . '/inc/customizer.php';
}
