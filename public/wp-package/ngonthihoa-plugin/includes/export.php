<?php
/**
 * Export/Import utilities for Ngon Thi Hoa
 * CSV export for reservations, contacts, job applications
 * JSON import support
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Export {

    public static function export_csv( $type ) {
        global $wpdb;

        switch ( $type ) {
            case 'reservations':
                $table  = $wpdb->prefix . 'nth_reservations';
                $rows   = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A );
                $filename = 'nth-reservations-' . date( 'Y-m-d' ) . '.csv';
                break;

            case 'contacts':
                $table  = $wpdb->prefix . 'nth_contacts';
                $rows   = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A );
                $filename = 'nth-contacts-' . date( 'Y-m-d' ) . '.csv';
                break;

            case 'applications':
                $table  = $wpdb->prefix . 'nth_applications';
                $rows   = $wpdb->get_results( "SELECT id, applicant_name, applicant_email, applicant_phone, job_title, status, language, created_at FROM {$table} ORDER BY created_at DESC", ARRAY_A );
                $filename = 'nth-applications-' . date( 'Y-m-d' ) . '.csv';
                break;

            default:
                wp_die( 'Invalid export type' );
        }

        if ( empty( $rows ) ) {
            wp_die( __( 'Không có dữ liệu để xuất.', NTH_TEXT_DOMAIN ) );
        }

        // Send CSV headers
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $output = fopen( 'php://output', 'w' );
        // BOM for Excel UTF-8 compatibility
        fputs( $output, "\xEF\xBB\xBF" );

        // Header row
        fputcsv( $output, array_keys( $rows[0] ) );

        // Data rows
        foreach ( $rows as $row ) {
            fputcsv( $output, $row );
        }

        fclose( $output );
        exit;
    }

    public static function export_json( $type ) {
        global $wpdb;
        $data = [];

        switch ( $type ) {
            case 'all':
                $data['reservations']  = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nth_reservations ORDER BY created_at DESC", ARRAY_A );
                $data['contacts']      = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nth_contacts ORDER BY created_at DESC", ARRAY_A );
                $data['applications']  = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nth_applications ORDER BY created_at DESC", ARRAY_A );
                $data['settings']      = self::get_exportable_settings();
                $data['exported_at']   = current_time( 'c' );
                break;

            case 'menu_items':
                $posts = get_posts( [
                    'post_type'      => 'menu_item',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                ] );
                foreach ( $posts as $post ) {
                    $meta = get_post_meta( $post->ID );
                    $cats = wp_get_post_terms( $post->ID, 'menu_category', [ 'fields' => 'slugs' ] );
                    $data[] = [
                        'title'       => $post->post_title,
                        'content'     => $post->post_content,
                        'status'      => $post->post_status,
                        'categories'  => $cats,
                        'price'       => $meta['_nth_price'][0] ?? '',
                        'price_unit'  => $meta['_nth_price_unit'][0] ?? '',
                        'description' => $meta['_nth_description'][0] ?? '',
                        'name_en'     => $meta['_nth_name_en'][0] ?? '',
                        'name_zh'     => $meta['_nth_name_zh'][0] ?? '',
                        'name_ko'     => $meta['_nth_name_ko'][0] ?? '',
                        'image'       => get_the_post_thumbnail_url( $post->ID, 'full' ),
                        'featured'    => (bool) ( $meta['_nth_featured'][0] ?? false ),
                    ];
                }
                break;
        }

        $filename = 'nth-export-' . $type . '-' . date( 'Y-m-d' ) . '.json';
        header( 'Content-Type: application/json; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        echo wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
        exit;
    }

    private static function get_exportable_settings() {
        $keys = [
            'nth_restaurant_name', 'nth_restaurant_phone', 'nth_restaurant_phone2',
            'nth_restaurant_phone3', 'nth_restaurant_email', 'nth_restaurant_address',
            'nth_restaurant_hours', 'nth_facebook_url', 'nth_tripadvisor_url',
            'nth_google_maps_url', 'nth_default_language', 'nth_languages_enabled',
        ];
        $settings = [];
        foreach ( $keys as $key ) {
            $settings[ $key ] = get_option( $key );
        }
        return $settings;
    }

    public static function import_json( $json_string ) {
        $data = json_decode( $json_string, true );
        if ( ! $data ) {
            return new WP_Error( 'invalid_json', __( 'JSON không hợp lệ.', NTH_TEXT_DOMAIN ) );
        }

        $imported = [];

        // Import settings
        if ( ! empty( $data['settings'] ) ) {
            foreach ( $data['settings'] as $key => $value ) {
                if ( strpos( $key, 'nth_' ) === 0 ) {
                    update_option( $key, $value );
                }
            }
            $imported['settings'] = true;
        }

        // Import menu items
        if ( ! empty( $data['menu_items'] ) ) {
            $count = 0;
            foreach ( $data['menu_items'] as $item ) {
                // Check if already exists
                $existing = get_page_by_title( sanitize_text_field( $item['title'] ), OBJECT, 'menu_item' );
                if ( ! $existing ) {
                    $post_id = wp_insert_post( [
                        'post_title'   => sanitize_text_field( $item['title'] ),
                        'post_content' => wp_kses_post( $item['content'] ?? '' ),
                        'post_type'    => 'menu_item',
                        'post_status'  => 'publish',
                    ] );
                    if ( $post_id && ! is_wp_error( $post_id ) ) {
                        if ( ! empty( $item['categories'] ) ) {
                            wp_set_post_terms( $post_id, $item['categories'], 'menu_category' );
                        }
                        foreach ( [ '_nth_price', '_nth_price_unit', '_nth_description', '_nth_name_en', '_nth_name_zh', '_nth_name_ko' ] as $meta_key ) {
                            $field = ltrim( $meta_key, '_nth_' );
                            if ( isset( $item[ $field ] ) ) {
                                update_post_meta( $post_id, $meta_key, sanitize_text_field( $item[ $field ] ) );
                            }
                        }
                        $count++;
                    }
                }
            }
            $imported['menu_items'] = $count;
        }

        return $imported;
    }
}

// Add import/export to admin menu
add_action( 'admin_menu', function() {
    add_submenu_page(
        'nth_dashboard',
        __( 'Import / Export', NTH_TEXT_DOMAIN ),
        __( 'Import / Export', NTH_TEXT_DOMAIN ),
        'manage_options',
        'nth_import_export',
        'nth_render_import_export_page'
    );
} );

function nth_render_import_export_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $message = '';

    // Handle JSON export
    if ( isset( $_GET['nth_json_export'] ) && check_admin_referer( 'nth_export_nonce' ) ) {
        NTH_Export::export_json( sanitize_key( $_GET['nth_json_export'] ) );
        exit;
    }

    // Handle JSON import
    if ( isset( $_POST['nth_import_json'] ) && check_admin_referer( 'nth_import_nonce' ) ) {
        if ( ! empty( $_FILES['nth_import_file']['tmp_name'] ) ) {
            $content = file_get_contents( $_FILES['nth_import_file']['tmp_name'] );
            $result  = NTH_Export::import_json( $content );
            if ( is_wp_error( $result ) ) {
                $message = '<div class="notice notice-error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
            } else {
                $message = '<div class="notice notice-success"><p>' . __( 'Import thành công!', NTH_TEXT_DOMAIN ) . '</p></div>';
            }
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e( 'Import / Export dữ liệu', NTH_TEXT_DOMAIN ); ?></h1>
        <?php echo $message; ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:900px;">
            <div class="postbox" style="padding:20px;">
                <h2><?php _e( 'Export dữ liệu', NTH_TEXT_DOMAIN ); ?></h2>
                <p><?php _e( 'Tải về toàn bộ dữ liệu nhà hàng dưới dạng JSON.', NTH_TEXT_DOMAIN ); ?></p>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=nth_import_export&nth_json_export=all' ), 'nth_export_nonce' ); ?>" class="button button-primary">
                    <?php _e( 'Export tất cả (JSON)', NTH_TEXT_DOMAIN ); ?>
                </a>
                <br><br>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=nth_import_export&nth_json_export=menu_items' ), 'nth_export_nonce' ); ?>" class="button">
                    <?php _e( 'Export menu items (JSON)', NTH_TEXT_DOMAIN ); ?>
                </a>
                <br><br>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=nth_analytics&nth_export=reservations' ), 'nth_export_nonce' ); ?>" class="button">
                    <?php _e( 'Export đặt bàn (CSV)', NTH_TEXT_DOMAIN ); ?>
                </a>
            </div>

            <div class="postbox" style="padding:20px;">
                <h2><?php _e( 'Import dữ liệu', NTH_TEXT_DOMAIN ); ?></h2>
                <p><?php _e( 'Import dữ liệu từ file JSON đã export trước đó.', NTH_TEXT_DOMAIN ); ?></p>
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'nth_import_nonce' ); ?>
                    <input type="file" name="nth_import_file" accept=".json" required />
                    <br><br>
                    <button type="submit" name="nth_import_json" value="1" class="button button-primary">
                        <?php _e( 'Import từ JSON', NTH_TEXT_DOMAIN ); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php
}
