<?php
/**
 * SEO Integration for Ngon Thi Hoa
 * Yoast SEO / Rank Math detection + fallback meta tags
 * Structured data (LocalBusiness, Restaurant, Menu)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_SEO {

    public function __construct() {
        add_action( 'wp_head', [ $this, 'output_structured_data' ], 5 );
        add_action( 'wp_head', [ $this, 'output_fallback_meta' ], 3 );
        add_filter( 'wp_title', [ $this, 'filter_title' ], 10, 2 );
        add_filter( 'the_seo_framework_title_from_custom_field', [ $this, 'tsf_title' ], 10, 2 );
        add_action( 'admin_menu', [ $this, 'add_seo_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_seo_settings' ] );
    }

    private function seo_plugin_active() {
        return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
    }

    public function output_fallback_meta() {
        if ( $this->seo_plugin_active() ) return; // Let Yoast/Rank Math handle it

        $name    = get_option( 'nth_restaurant_name', 'Ngon Thị Hoa' );
        $desc    = get_option( 'nth_seo_description', 'Nhà hàng Ngon Thị Hoa - Ẩm thực Việt Nam đặc sắc tại Đà Nẵng. Đặt bàn: 02366 515 100.' );
        $img     = get_option( 'nth_og_image', get_template_directory_uri() . '/assets/images/og-image.jpg' );
        $url     = home_url( '/' );

        // Open Graph
        ?>
<meta property="og:type" content="restaurant" />
<meta property="og:site_name" content="<?php echo esc_attr( $name ); ?>" />
<meta property="og:title" content="<?php echo esc_attr( is_front_page() ? $name : ( get_the_title() . ' - ' . $name ) ); ?>" />
<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>" />
<meta property="og:image" content="<?php echo esc_url( $img ); ?>" />
<meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?php echo esc_attr( $name ); ?>" />
<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>" />
<meta name="twitter:image" content="<?php echo esc_url( $img ); ?>" />
<?php
    }

    public function output_structured_data() {
        $name    = get_option( 'nth_restaurant_name', 'Ngon Thị Hoa' );
        $phone   = get_option( 'nth_restaurant_phone', '02366 515 100' );
        $email   = get_option( 'nth_restaurant_email', 'info@ngonthihoarestaurant.com' );
        $address = get_option( 'nth_restaurant_address', 'Đà Nẵng, Việt Nam' );
        $hours   = get_option( 'nth_restaurant_hours', '06:30-22:00' );
        $fb      = get_option( 'nth_facebook_url' );
        $ta      = get_option( 'nth_tripadvisor_url' );

        $sameAs = array_filter( [ $fb, $ta ] );

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => ['Restaurant', 'LocalBusiness'],
            'name'        => $name,
            'description' => 'Nhà hàng Ngon Thị Hoa - Ẩm thực Việt Nam đặc sắc tại Đà Nẵng với hơn 200 món ăn. Thực đơn phong phú từ bữa sáng đến bữa tối.',
            'url'         => home_url( '/' ),
            'telephone'   => $phone,
            'email'       => $email,
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => 'Đà Nẵng',
                'addressCountry'  => 'VN',
                'streetAddress'   => $address,
            ],
            'geo' => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => 16.0544,
                'longitude' => 108.2022,
            ],
            'openingHoursSpecification' => [
                [
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => [ 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday' ],
                    'opens'     => '06:30',
                    'closes'    => '22:00',
                ],
            ],
            'servesCuisine' => [ 'Vietnamese', 'Ẩm thực Việt Nam' ],
            'priceRange'    => '$$',
            'currenciesAccepted' => 'VND',
            'paymentAccepted'    => 'Cash, Credit Card',
            'image'         => get_template_directory_uri() . '/assets/images/og-image.jpg',
            'sameAs'        => array_values( $sameAs ),
            'hasMenu'       => home_url( '/menu' ),
            'acceptsReservations' => 'True',
        ];

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n";
    }

    public function filter_title( $title, $sep ) {
        if ( is_front_page() ) {
            return get_option( 'nth_restaurant_name', 'Ngon Thị Hoa' ) . ' - Nhà hàng Đà Nẵng';
        }
        return $title;
    }

    public function tsf_title( $title, $args ) {
        return $this->filter_title( $title, '' );
    }

    public function add_seo_settings_page() {
        add_submenu_page(
            'nth_dashboard',
            __( 'SEO & Tracking', NTH_TEXT_DOMAIN ),
            __( 'SEO & Tracking', NTH_TEXT_DOMAIN ),
            'manage_options',
            'nth_seo_settings',
            [ $this, 'render_settings_page' ]
        );
    }

    public function register_seo_settings() {
        $fields = [
            'nth_seo_description', 'nth_og_image',
            'nth_ga4_id', 'nth_gtm_id', 'nth_fb_pixel_id',
            'nth_google_ads_id', 'nth_google_ads_label',
            'nth_gsc_verification',
        ];
        foreach ( $fields as $field ) {
            register_setting( 'nth_seo_group', $field, [ 'sanitize_callback' => 'sanitize_text_field' ] );
        }
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'nth_seo_settings-options' ) ) {
            // handled by register_setting / options.php
        }
        ?>
        <div class="wrap">
            <h1><?php _e( 'SEO & Analytics Tracking', NTH_TEXT_DOMAIN ); ?></h1>

            <?php if ( $this->seo_plugin_active() ) : ?>
            <div class="notice notice-success"><p><?php _e( 'Đã phát hiện plugin SEO (Yoast / Rank Math). Meta tags SEO được quản lý bởi plugin đó.', NTH_TEXT_DOMAIN ); ?></p></div>
            <?php else : ?>
            <div class="notice notice-warning"><p><?php _e( 'Chưa cài Yoast SEO hoặc Rank Math. Plugin này sẽ tự động thêm meta tags và Schema cơ bản.', NTH_TEXT_DOMAIN ); ?></p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'nth_seo_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Meta Description mặc định', NTH_TEXT_DOMAIN ); ?></th>
                        <td><textarea name="nth_seo_description" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'nth_seo_description' ) ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Hình ảnh OG mặc định (URL)', NTH_TEXT_DOMAIN ); ?></th>
                        <td>
                            <input type="url" name="nth_og_image" value="<?php echo esc_url( get_option( 'nth_og_image' ) ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Hình hiển thị khi chia sẻ link lên mạng xã hội', NTH_TEXT_DOMAIN ); ?></p>
                        </td>
                    </tr>
                    <tr><th colspan="2"><h2><?php _e( 'Google Analytics 4', NTH_TEXT_DOMAIN ); ?></h2></th></tr>
                    <tr>
                        <th><?php _e( 'GA4 Measurement ID', NTH_TEXT_DOMAIN ); ?></th>
                        <td>
                            <input type="text" name="nth_ga4_id" value="<?php echo esc_attr( get_option( 'nth_ga4_id' ) ); ?>" placeholder="G-XXXXXXXXXX" class="regular-text" />
                            <p class="description"><?php _e( 'Ví dụ: G-ABC123XYZ. Tìm trong Google Analytics → Admin → Data Streams', NTH_TEXT_DOMAIN ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Google Tag Manager ID', NTH_TEXT_DOMAIN ); ?></th>
                        <td><input type="text" name="nth_gtm_id" value="<?php echo esc_attr( get_option( 'nth_gtm_id' ) ); ?>" placeholder="GTM-XXXXXXX" class="regular-text" /></td>
                    </tr>
                    <tr><th colspan="2"><h2><?php _e( 'Google Search Console', NTH_TEXT_DOMAIN ); ?></h2></th></tr>
                    <tr>
                        <th><?php _e( 'Verification Code', NTH_TEXT_DOMAIN ); ?></th>
                        <td>
                            <input type="text" name="nth_gsc_verification" value="<?php echo esc_attr( get_option( 'nth_gsc_verification' ) ); ?>" placeholder="abc123xyz..." class="regular-text" />
                            <p class="description"><?php _e( 'Chỉ nhập phần content="..." trong thẻ meta verification của Google Search Console', NTH_TEXT_DOMAIN ); ?></p>
                        </td>
                    </tr>
                    <tr><th colspan="2"><h2><?php _e( 'Facebook / Meta Pixel', NTH_TEXT_DOMAIN ); ?></h2></th></tr>
                    <tr>
                        <th><?php _e( 'Facebook Pixel ID', NTH_TEXT_DOMAIN ); ?></th>
                        <td><input type="text" name="nth_fb_pixel_id" value="<?php echo esc_attr( get_option( 'nth_fb_pixel_id' ) ); ?>" placeholder="1234567890" class="regular-text" /></td>
                    </tr>
                    <tr><th colspan="2"><h2><?php _e( 'Google Ads', NTH_TEXT_DOMAIN ); ?></h2></th></tr>
                    <tr>
                        <th><?php _e( 'Google Ads Conversion ID', NTH_TEXT_DOMAIN ); ?></th>
                        <td><input type="text" name="nth_google_ads_id" value="<?php echo esc_attr( get_option( 'nth_google_ads_id' ) ); ?>" placeholder="AW-XXXXXXXXX" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Google Ads Conversion Label', NTH_TEXT_DOMAIN ); ?></th>
                        <td><input type="text" name="nth_google_ads_label" value="<?php echo esc_attr( get_option( 'nth_google_ads_label' ) ); ?>" placeholder="xxxxxxxxxxxxxx" class="regular-text" /></td>
                    </tr>
                </table>
                <?php submit_button( __( 'Lưu cài đặt', NTH_TEXT_DOMAIN ) ); ?>
            </form>
        </div>
        <?php
    }
}

new NTH_SEO();
