<?php
/**
 * Custom Post Types for Ngon Thi Hoa
 * menu_item, blog_post, media_gallery, job_position
 * Compatible with WPML and Polylang
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function nth_register_post_types() {

    // ── MENU ITEMS ─────────────────────────────────────────────────────────────
    register_post_type( 'menu_item', [
        'labels' => [
            'name'               => __( 'Món ăn', NTH_TEXT_DOMAIN ),
            'singular_name'      => __( 'Món ăn', NTH_TEXT_DOMAIN ),
            'add_new'            => __( 'Thêm món mới', NTH_TEXT_DOMAIN ),
            'add_new_item'       => __( 'Thêm món ăn', NTH_TEXT_DOMAIN ),
            'edit_item'          => __( 'Chỉnh sửa món ăn', NTH_TEXT_DOMAIN ),
            'view_item'          => __( 'Xem món ăn', NTH_TEXT_DOMAIN ),
            'search_items'       => __( 'Tìm món ăn', NTH_TEXT_DOMAIN ),
            'not_found'          => __( 'Không tìm thấy món ăn.', NTH_TEXT_DOMAIN ),
            'all_items'          => __( 'Tất cả món ăn', NTH_TEXT_DOMAIN ),
            'menu_name'          => __( 'Thực đơn', NTH_TEXT_DOMAIN ),
        ],
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,  // Gutenberg + REST API support
        'menu_position'       => 26,
        'menu_icon'           => 'dashicons-food',
        'supports'            => [ 'title', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ],
        'taxonomies'          => [ 'menu_category', 'menu_group' ],
        'has_archive'         => false,
        'rewrite'             => [ 'slug' => 'mon-an' ],
        'capability_type'     => 'post',
    ] );

    // ── BLOG POSTS ─────────────────────────────────────────────────────────────
    register_post_type( 'blog_post', [
        'labels' => [
            'name'               => __( 'Tin tức & Blog', NTH_TEXT_DOMAIN ),
            'singular_name'      => __( 'Bài viết', NTH_TEXT_DOMAIN ),
            'add_new'            => __( 'Thêm bài viết', NTH_TEXT_DOMAIN ),
            'add_new_item'       => __( 'Thêm bài viết mới', NTH_TEXT_DOMAIN ),
            'edit_item'          => __( 'Chỉnh sửa bài viết', NTH_TEXT_DOMAIN ),
            'all_items'          => __( 'Tất cả bài viết', NTH_TEXT_DOMAIN ),
            'menu_name'          => __( 'Tin tức & Blog', NTH_TEXT_DOMAIN ),
        ],
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'menu_position'       => 27,
        'menu_icon'           => 'dashicons-admin-post',
        'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments', 'revisions', 'custom-fields' ],
        'taxonomies'          => [ 'blog_category', 'post_tag' ],
        'has_archive'         => 'tin-tuc',
        'rewrite'             => [ 'slug' => 'bai-viet' ],
        'capability_type'     => 'post',
    ] );

    // ── MEDIA GALLERY ──────────────────────────────────────────────────────────
    register_post_type( 'media_gallery', [
        'labels' => [
            'name'               => __( 'Hình ảnh & Video', NTH_TEXT_DOMAIN ),
            'singular_name'      => __( 'Ảnh/Video', NTH_TEXT_DOMAIN ),
            'add_new'            => __( 'Thêm media', NTH_TEXT_DOMAIN ),
            'add_new_item'       => __( 'Thêm hình ảnh / video mới', NTH_TEXT_DOMAIN ),
            'edit_item'          => __( 'Chỉnh sửa', NTH_TEXT_DOMAIN ),
            'all_items'          => __( 'Tất cả media', NTH_TEXT_DOMAIN ),
            'menu_name'          => __( 'Hình ảnh & Video', NTH_TEXT_DOMAIN ),
        ],
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'menu_position'       => 28,
        'menu_icon'           => 'dashicons-format-gallery',
        'supports'            => [ 'title', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ],
        'taxonomies'          => [ 'media_category' ],
        'has_archive'         => false,
        'rewrite'             => [ 'slug' => 'hinh-anh' ],
        'capability_type'     => 'post',
    ] );

    // ── JOB POSITIONS ──────────────────────────────────────────────────────────
    register_post_type( 'job_position', [
        'labels' => [
            'name'               => __( 'Tuyển dụng', NTH_TEXT_DOMAIN ),
            'singular_name'      => __( 'Vị trí tuyển dụng', NTH_TEXT_DOMAIN ),
            'add_new'            => __( 'Thêm vị trí mới', NTH_TEXT_DOMAIN ),
            'add_new_item'       => __( 'Thêm vị trí tuyển dụng', NTH_TEXT_DOMAIN ),
            'edit_item'          => __( 'Chỉnh sửa vị trí', NTH_TEXT_DOMAIN ),
            'all_items'          => __( 'Tất cả vị trí', NTH_TEXT_DOMAIN ),
            'menu_name'          => __( 'Tuyển dụng', NTH_TEXT_DOMAIN ),
        ],
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'menu_position'       => 29,
        'menu_icon'           => 'dashicons-businessperson',
        'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' ],
        'has_archive'         => false,
        'rewrite'             => [ 'slug' => 'tuyen-dung' ],
        'capability_type'     => 'post',
    ] );
}
add_action( 'init', 'nth_register_post_types' );

/**
 * Register custom post types with WPML (if active)
 */
add_filter( 'wpml_translatable_documents', function( $types ) {
    $types['menu_item']     = new stdClass();
    $types['blog_post']     = new stdClass();
    $types['media_gallery'] = new stdClass();
    $types['job_position']  = new stdClass();
    return $types;
} );

/**
 * Register post types with Polylang (if active)
 */
add_filter( 'pll_get_post_types', function( $post_types ) {
    $post_types['menu_item']     = 'menu_item';
    $post_types['blog_post']     = 'blog_post';
    $post_types['media_gallery'] = 'media_gallery';
    $post_types['job_position']  = 'job_position';
    return $post_types;
}, 10, 2 );

/**
 * Add custom columns to menu_item list table
 */
add_filter( 'manage_menu_item_posts_columns', function( $columns ) {
    $new = [];
    foreach ( $columns as $key => $value ) {
        $new[ $key ] = $value;
        if ( $key === 'title' ) {
            $new['menu_group']    = __( 'Nhóm thực đơn', NTH_TEXT_DOMAIN );
            $new['price']         = __( 'Giá', NTH_TEXT_DOMAIN );
            $new['menu_category'] = __( 'Danh mục', NTH_TEXT_DOMAIN );
        }
    }
    return $new;
} );

add_action( 'manage_menu_item_posts_custom_column', function( $column, $post_id ) {
    switch ( $column ) {
        case 'price':
            $price = get_post_meta( $post_id, '_nth_price', true );
            $unit  = get_post_meta( $post_id, '_nth_price_unit', true );
            echo $price ? esc_html( number_format( $price ) . 'đ' . ( $unit ? '/' . $unit : '' ) ) : '—';
            break;
        case 'menu_group':
            $group = get_post_meta( $post_id, '_nth_menu_group', true );
            $groups = [ 'sang' => 'Sáng', 'trua_toi' => 'Trưa & Tối', 'do_uong' => 'Đồ uống', 'do_uong_co_con' => 'Đồ uống có cồn', 'ruou_vang' => 'Rượu vang' ];
            echo esc_html( $groups[ $group ] ?? $group );
            break;
        case 'menu_category':
            $terms = get_the_terms( $post_id, 'menu_category' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                echo esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) );
            }
            break;
    }
}, 10, 2 );
