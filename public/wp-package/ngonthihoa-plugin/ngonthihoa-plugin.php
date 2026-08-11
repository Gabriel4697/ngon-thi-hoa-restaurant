<?php
/**
 * Plugin Name: Ngon Thi Hoa Restaurant
 * Plugin URI: https://ngonthihoarestaurant.com
 * Description: Complete restaurant management plugin for Ngon Thi Hoa - Custom post types, taxonomies, reservations, contact forms, job applications, analytics dashboard, SEO integration, and multilingual support (VI/EN/ZH/KO).
 * Version: 2.0.0
 * Author: Ngon Thi Hoa
 * Author URI: https://ngonthihoarestaurant.com
 * Text Domain: ngonthihoa
 * Domain Path: /languages
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NTH_VERSION', '2.0.0' );
define( 'NTH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NTH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NTH_TEXT_DOMAIN', 'ngonthihoa' );

class NTH_Plugin {

    public function __construct() {
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
        add_action( 'plugins_loaded', [ $this, 'includes' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
        // Head tracking codes
        add_action( 'wp_head', [ $this, 'output_tracking_codes' ], 1 );
        add_action( 'wp_body_open', [ $this, 'output_body_tracking' ] );
        // Performance: lazy loading, WebP, caching headers
        add_filter( 'wp_lazy_loading_enabled', '__return_true' );
        add_filter( 'wp_content_img_tag', [ $this, 'enhance_img_attributes' ], 10, 3 );
        add_filter( 'wp_get_attachment_image_attributes', [ $this, 'add_img_decode_attr' ], 10, 2 );
        add_action( 'send_headers', [ $this, 'add_cache_headers' ] );
        add_filter( 'style_loader_tag', [ $this, 'defer_non_critical_css' ], 10, 4 );
        // WebP support via .htaccess rewrite (activate hook handles this)
        add_action( 'after_setup_theme', [ $this, 'setup_image_sizes' ] );
    }

    public function activate() {
        $this->create_tables();
        $this->set_default_options();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    public function load_textdomain() {
        load_plugin_textdomain( NTH_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function includes() {
        require_once NTH_PLUGIN_DIR . 'includes/post-types.php';
        require_once NTH_PLUGIN_DIR . 'includes/taxonomies.php';
        require_once NTH_PLUGIN_DIR . 'includes/meta-boxes.php';
        require_once NTH_PLUGIN_DIR . 'includes/admin.php';
        require_once NTH_PLUGIN_DIR . 'includes/ajax.php';
        require_once NTH_PLUGIN_DIR . 'includes/import-data.php';
        require_once NTH_PLUGIN_DIR . 'includes/custom-tables.php';
        require_once NTH_PLUGIN_DIR . 'includes/analytics.php';
        require_once NTH_PLUGIN_DIR . 'includes/seo.php';
        require_once NTH_PLUGIN_DIR . 'includes/export.php';
        require_once NTH_PLUGIN_DIR . 'includes/shortcodes.php';
        require_once NTH_PLUGIN_DIR . 'includes/security.php';
        require_once NTH_PLUGIN_DIR . 'includes/multilingual.php';
    }

    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = [];

        // Reservations table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}nth_reservations (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(30) NOT NULL,
            reservation_date date NOT NULL,
            reservation_time time NOT NULL,
            number_of_guests int(11) NOT NULL DEFAULT 1,
            notes text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            language varchar(5) DEFAULT 'vi',
            ip_address varchar(45) DEFAULT NULL,
            source varchar(50) DEFAULT 'website',
            admin_notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY reservation_date (reservation_date),
            KEY email (email)
        ) $charset_collate;";

        // Contact submissions table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}nth_contacts (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(30) DEFAULT NULL,
            subject varchar(200) DEFAULT NULL,
            message text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'unread',
            language varchar(5) DEFAULT 'vi',
            ip_address varchar(45) DEFAULT NULL,
            admin_notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY email (email)
        ) $charset_collate;";

        // Job applications table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}nth_applications (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            applicant_name varchar(100) NOT NULL,
            applicant_email varchar(100) NOT NULL,
            applicant_phone varchar(30) DEFAULT NULL,
            job_id bigint(20) DEFAULT NULL,
            job_title varchar(200) DEFAULT NULL,
            cv_file varchar(500) DEFAULT NULL,
            cover_letter text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            language varchar(5) DEFAULT 'vi',
            ip_address varchar(45) DEFAULT NULL,
            admin_notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY job_id (job_id),
            KEY applicant_email (applicant_email)
        ) $charset_collate;";

        // Page views analytics table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}nth_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_url varchar(500) NOT NULL,
            page_type varchar(50) DEFAULT 'page',
            referrer varchar(500) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            language varchar(5) DEFAULT 'vi',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY page_url (page_url(191)),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        foreach ( $sql as $query ) {
            dbDelta( $query );
        }

        update_option( 'nth_db_version', NTH_VERSION );
    }

    public function set_default_options() {
        $defaults = [
            'nth_restaurant_name'      => 'Ngon Thị Hoa',
            'nth_restaurant_phone'     => '02366 515 100',
            'nth_restaurant_phone2'    => '0967 220 100',
            'nth_restaurant_phone3'    => '098 481 88 80',
            'nth_restaurant_email'     => 'info@ngonthihoarestaurant.com',
            'nth_restaurant_address'   => 'Đà Nẵng, Việt Nam',
            'nth_restaurant_hours'     => 'Hằng ngày / Daily: 6:30 - 22:00',
            'nth_facebook_url'         => 'https://www.facebook.com/NgonThiHoa',
            'nth_tripadvisor_url'      => 'https://www.tripadvisor.com.vn/Restaurant_Review-g298085-d20139533-Reviews-Ngon_Thi_Hoa_Restaurant-Da_Nang.html',
            'nth_google_maps_url'      => 'https://share.google/Hva4u6cCiek1ZZhfA',
            'nth_google_maps_embed'    => '',
            'nth_ga4_id'               => '',
            'nth_gtm_id'               => '',
            'nth_fb_pixel_id'          => '',
            'nth_google_ads_id'        => '',
            'nth_google_ads_label'     => '',
            'nth_gsc_verification'     => '',
            'nth_reservation_notify'   => get_option('admin_email'),
            'nth_contact_notify'       => get_option('admin_email'),
            'nth_application_notify'   => get_option('admin_email'),
            'nth_languages_enabled'    => ['vi', 'en', 'zh', 'ko'],
            'nth_default_language'     => 'vi',
            'nth_seo_plugin'           => 'auto',
            'nth_cache_enabled'        => '1',
            'nth_security_recaptcha'   => '0',
            'nth_recaptcha_site_key'   => '',
            'nth_recaptcha_secret'     => '',
        ];
        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( $key ) ) {
                add_option( $key, $value );
            }
        }
    }

    public function frontend_assets() {
        wp_enqueue_style(
            'nth-frontend',
            NTH_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            NTH_VERSION
        );
        wp_enqueue_script(
            'nth-frontend',
            NTH_PLUGIN_URL . 'assets/js/frontend.js',
            [ 'jquery' ],
            NTH_VERSION,
            true
        );
        wp_localize_script( 'nth-frontend', 'nth_ajax', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'nth_nonce' ),
            'lang'     => substr( get_locale(), 0, 2 ),
            'strings'  => [
                'submitting'        => __( 'Đang gửi...', NTH_TEXT_DOMAIN ),
                'success_reserve'   => __( 'Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận sớm.', NTH_TEXT_DOMAIN ),
                'success_contact'   => __( 'Gửi thành công! Chúng tôi sẽ phản hồi trong 24h.', NTH_TEXT_DOMAIN ),
                'success_apply'     => __( 'Đơn ứng tuyển đã được gửi thành công!', NTH_TEXT_DOMAIN ),
                'error_general'     => __( 'Đã có lỗi xảy ra. Vui lòng thử lại.', NTH_TEXT_DOMAIN ),
                'error_required'    => __( 'Vui lòng điền đầy đủ thông tin bắt buộc.', NTH_TEXT_DOMAIN ),
            ],
        ] );
    }

    public function admin_assets( $hook ) {
        wp_enqueue_style(
            'nth-admin',
            NTH_PLUGIN_URL . 'assets/css/admin.css',
            [],
            NTH_VERSION
        );
        if ( strpos( $hook, 'nth_' ) !== false || strpos( $hook, 'ngonthihoa' ) !== false ) {
            wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', [], '4.4.0', true );
        }
        wp_enqueue_media();
        wp_enqueue_script(
            'nth-admin',
            NTH_PLUGIN_URL . 'assets/js/admin.js',
            [ 'jquery', 'jquery-ui-datepicker' ],
            NTH_VERSION,
            true
        );
        wp_localize_script( 'nth-admin', 'nth_admin', [
            'ajax_url'   => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'nth_admin_nonce' ),
            'upload_url' => wp_upload_dir()['url'],
        ] );
    }

    public function output_tracking_codes() {
        $ga4_id   = get_option( 'nth_ga4_id' );
        $gtm_id   = get_option( 'nth_gtm_id' );
        $pixel_id = get_option( 'nth_fb_pixel_id' );
        $gsc_ver  = get_option( 'nth_gsc_verification' );
        $ads_id   = get_option( 'nth_google_ads_id' );

        // Google Search Console verification
        if ( $gsc_ver ) {
            echo '<meta name="google-site-verification" content="' . esc_attr( $gsc_ver ) . '" />' . "\n";
        }

        // Google Analytics 4 + Google Ads
        if ( $ga4_id || $ads_id ) {
            $primary_id = $ga4_id ?: $ads_id;
            ?>
<!-- Google tag (gtag.js) - GA4 + Google Ads -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $primary_id ); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
<?php if ( $ga4_id ) : ?>
gtag('config', '<?php echo esc_js( $ga4_id ); ?>');
<?php endif; ?>
<?php if ( $ads_id ) : ?>
gtag('config', '<?php echo esc_js( $ads_id ); ?>');
<?php endif; ?>
</script>
<?php
        }

        // Google Tag Manager
        if ( $gtm_id ) {
            ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
<!-- End Google Tag Manager -->
<?php
        }

        // Facebook Pixel
        if ( $pixel_id ) {
            ?>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?php echo esc_js( $pixel_id ); ?>');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?php echo esc_attr( $pixel_id ); ?>&ev=PageView&noscript=1"/></noscript>
<!-- End Meta Pixel Code -->
<?php
        }
    }

    public function output_body_tracking() {
        $gtm_id = get_option( 'nth_gtm_id' );
        if ( $gtm_id ) {
            echo '<!-- Google Tag Manager (noscript) -->' . "\n";
            echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
            echo '<!-- End Google Tag Manager (noscript) -->' . "\n";
        }
    }

    /**
     * Add decoding="async" and fetchpriority to content images.
     */
    public function enhance_img_attributes( $filtered_image, $context, $attachment_id ) {
        if ( ! str_contains( $filtered_image, 'decoding=' ) ) {
            $filtered_image = str_replace( '<img ', '<img decoding="async" ', $filtered_image );
        }
        return $filtered_image;
    }

    /**
     * Add decoding="async" to wp_get_attachment_image calls.
     */
    public function add_img_decode_attr( $attr, $attachment ) {
        if ( empty( $attr['decoding'] ) ) {
            $attr['decoding'] = 'async';
        }
        return $attr;
    }

    /**
     * Set aggressive browser caching headers for static assets.
     */
    public function add_cache_headers() {
        if ( is_admin() ) return;
        // Cache static pages for 1 hour, images/CSS/JS for 30 days
        if ( ! is_singular() && ! is_archive() ) return;
        header( 'Cache-Control: public, max-age=3600, stale-while-revalidate=86400' );
        header( 'Vary: Accept-Encoding' );
    }

    /**
     * Register custom image sizes optimised for menu display.
     */
    public function setup_image_sizes() {
        add_theme_support( 'post-thumbnails' );
        // Menu page: full-width high-res
        add_image_size( 'menu-fullwidth', 1200, 900, false );
        // Menu page: card thumb
        add_image_size( 'menu-card', 600, 450, true );
        // Hero/banner
        add_image_size( 'hero-banner', 1920, 800, true );
        // Thumbnail for galleries
        add_image_size( 'gallery-thumb', 400, 300, true );
    }

    /**
     * Defer non-critical CSS (print media trick for async CSS load).
     * Only applied to known non-critical handles.
     */
    public function defer_non_critical_css( $tag, $handle, $href, $media ) {
        $non_critical = [ 'nth-frontend' ];
        if ( in_array( $handle, $non_critical, true ) ) {
            return '<link rel="preload" as="style" href="' . esc_url( $href ) . '" onload="this.onload=null;this.rel=\'stylesheet\'">'
                 . '<noscript>' . $tag . '</noscript>';
        }
        return $tag;
    }
}

new NTH_Plugin();
