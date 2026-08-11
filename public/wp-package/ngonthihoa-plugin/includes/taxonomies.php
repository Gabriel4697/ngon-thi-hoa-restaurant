<?php
/**
 * Custom Taxonomies for Ngon Thi Hoa
 * menu_category, menu_group, blog_category, media_category
 * With WPML and Polylang compatibility
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function nth_register_taxonomies() {

    // ── MENU GROUP (top-level: Sáng / Trưa-Tối / Đồ Uống / etc.) ────────────
    register_taxonomy( 'menu_group', 'menu_item', [
        'labels' => [
            'name'              => __( 'Nhóm thực đơn', NTH_TEXT_DOMAIN ),
            'singular_name'     => __( 'Nhóm', NTH_TEXT_DOMAIN ),
            'add_new_item'      => __( 'Thêm nhóm', NTH_TEXT_DOMAIN ),
            'edit_item'         => __( 'Chỉnh sửa nhóm', NTH_TEXT_DOMAIN ),
            'all_items'         => __( 'Tất cả nhóm', NTH_TEXT_DOMAIN ),
            'search_items'      => __( 'Tìm nhóm', NTH_TEXT_DOMAIN ),
            'menu_name'         => __( 'Nhóm thực đơn', NTH_TEXT_DOMAIN ),
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'nhom-menu' ],
    ] );

    // ── MENU CATEGORY ─────────────────────────────────────────────────────────
    register_taxonomy( 'menu_category', 'menu_item', [
        'labels' => [
            'name'              => __( 'Danh mục thực đơn', NTH_TEXT_DOMAIN ),
            'singular_name'     => __( 'Danh mục', NTH_TEXT_DOMAIN ),
            'add_new_item'      => __( 'Thêm danh mục', NTH_TEXT_DOMAIN ),
            'edit_item'         => __( 'Chỉnh sửa danh mục', NTH_TEXT_DOMAIN ),
            'all_items'         => __( 'Tất cả danh mục', NTH_TEXT_DOMAIN ),
            'search_items'      => __( 'Tìm danh mục', NTH_TEXT_DOMAIN ),
            'menu_name'         => __( 'Danh mục', NTH_TEXT_DOMAIN ),
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'danh-muc-mon' ],
    ] );

    // ── BLOG CATEGORY ─────────────────────────────────────────────────────────
    register_taxonomy( 'blog_category', 'blog_post', [
        'labels' => [
            'name'              => __( 'Danh mục blog', NTH_TEXT_DOMAIN ),
            'singular_name'     => __( 'Danh mục', NTH_TEXT_DOMAIN ),
            'add_new_item'      => __( 'Thêm danh mục', NTH_TEXT_DOMAIN ),
            'all_items'         => __( 'Tất cả danh mục', NTH_TEXT_DOMAIN ),
            'menu_name'         => __( 'Danh mục', NTH_TEXT_DOMAIN ),
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'danh-muc-blog' ],
    ] );

    // ── MEDIA CATEGORY ────────────────────────────────────────────────────────
    register_taxonomy( 'media_category', 'media_gallery', [
        'labels' => [
            'name'              => __( 'Danh mục media', NTH_TEXT_DOMAIN ),
            'singular_name'     => __( 'Danh mục', NTH_TEXT_DOMAIN ),
            'add_new_item'      => __( 'Thêm danh mục', NTH_TEXT_DOMAIN ),
            'all_items'         => __( 'Tất cả danh mục', NTH_TEXT_DOMAIN ),
            'menu_name'         => __( 'Danh mục media', NTH_TEXT_DOMAIN ),
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'danh-muc-media' ],
    ] );
}
add_action( 'init', 'nth_register_taxonomies' );

/**
 * Seed default menu terms on activation (only if not yet seeded)
 */
function nth_seed_default_terms() {
    if ( get_option( 'nth_terms_seeded' ) ) return;

    $menu_groups = [
        'sang'            => 'Sáng (Breakfast)',
        'trua_toi'        => 'Trưa và Tối (Lunch & Dinner)',
        'do_uong'         => 'Đồ Uống (Beverages)',
        'do_uong_co_con'  => 'Đồ Uống Có Cồn (Alcoholic)',
        'ruou_vang'       => 'Rượu Vang (Wine)',
    ];

    foreach ( $menu_groups as $slug => $name ) {
        if ( ! term_exists( $slug, 'menu_group' ) ) {
            wp_insert_term( $name, 'menu_group', [ 'slug' => $slug ] );
        }
    }

    $menu_categories = [
        // Sáng
        'pho-bun-mi'      => 'Phở / Bún / Mì',
        'com-bua-sang'    => 'Cơm bữa sáng',
        'banh-mi'         => 'Bánh mì',
        'trang-mien'      => 'Tráng miệng sáng',
        'kem-cac-loai'    => 'Kem các loại',
        // Trưa - Tối
        'khai-vi'         => 'Khai vị & Salad',
        'mon-truyen-thong' => 'Món truyền thống',
        'lau'             => 'Lẩu',
        'ca'              => 'Cá',
        'tom-cua'         => 'Tôm / Cua / Mực',
        'thit-heo'        => 'Thịt heo',
        'thit-bo'         => 'Thịt bò',
        'ga-vit'          => 'Gà / Vịt',
        'rau-cu'          => 'Rau củ',
        'com-chao'        => 'Cơm / Cháo',
        'bun-mi-toi'      => 'Bún / Mì buổi tối',
        'sup-canh'        => 'Súp / Canh',
        'hai-san'         => 'Hải sản đặc biệt',
        // Đồ uống
        'nuoc-ep'         => 'Nước ép & Sinh tố',
        'tra-sua'         => 'Trà & Sữa',
        'ca-phe'          => 'Cà phê',
        'nuoc-co-ga'      => 'Nước có ga',
        // Đồ uống có cồn
        'bia'             => 'Bia (Beer)',
        'ruou-manh'       => 'Rượu mạnh (Spirits)',
        'cocktail'        => 'Cocktail',
        // Rượu vang
        'vang-do'         => 'Rượu vang đỏ',
        'vang-trang'      => 'Rượu vang trắng',
        'champagne'       => 'Champagne',
        'rose'            => 'Rosé Wine',
        'sparkling'       => 'Sparkling Wine',
    ];

    foreach ( $menu_categories as $slug => $name ) {
        if ( ! term_exists( $slug, 'menu_category' ) ) {
            wp_insert_term( $name, 'menu_category', [ 'slug' => $slug ] );
        }
    }

    $blog_cats = [
        'tin-tuc'         => 'Tin tức',
        'su-kien'         => 'Sự kiện',
        'chuyen-bep'      => 'Chuyện bếp',
        'khuyen-mai'      => 'Khuyến mãi',
    ];

    foreach ( $blog_cats as $slug => $name ) {
        if ( ! term_exists( $slug, 'blog_category' ) ) {
            wp_insert_term( $name, 'blog_category', [ 'slug' => $slug ] );
        }
    }

    $media_cats = [
        'nha-hang'        => 'Nhà hàng',
        'mon-an'          => 'Món ăn',
        'su-kien-media'   => 'Sự kiện',
        'video'           => 'Video',
    ];

    foreach ( $media_cats as $slug => $name ) {
        if ( ! term_exists( $slug, 'media_category' ) ) {
            wp_insert_term( $name, 'media_category', [ 'slug' => $slug ] );
        }
    }

    update_option( 'nth_terms_seeded', true );
}
add_action( 'init', 'nth_seed_default_terms', 20 );

// Register taxonomies with Polylang
add_filter( 'pll_get_taxonomies', function( $taxonomies ) {
    $taxonomies['menu_group']    = 'menu_group';
    $taxonomies['menu_category'] = 'menu_category';
    $taxonomies['blog_category'] = 'blog_category';
    $taxonomies['media_category'] = 'media_category';
    return $taxonomies;
}, 10, 2 );
