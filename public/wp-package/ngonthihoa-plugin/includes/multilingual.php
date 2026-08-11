<?php
/**
 * Multilingual support for Ngon Thi Hoa (VI / EN / ZH / KO)
 * Compatible with WPML, Polylang, and standalone
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Multilingual {

    public static $languages = [
        'vi' => [ 'name' => 'Tiếng Việt', 'flag' => '🇻🇳', 'locale' => 'vi' ],
        'en' => [ 'name' => 'English',    'flag' => '🇬🇧', 'locale' => 'en_US' ],
        'zh' => [ 'name' => '中文',        'flag' => '🇨🇳', 'locale' => 'zh_CN' ],
        'ko' => [ 'name' => '한국어',      'flag' => '🇰🇷', 'locale' => 'ko_KR' ],
    ];

    public function __construct() {
        add_action( 'init', [ $this, 'setup_polylang_strings' ] );
        add_action( 'wp_footer', [ $this, 'inject_language_switcher_script' ] );
        add_action( 'admin_menu', [ $this, 'add_multilingual_settings' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        // REST API language param
        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
    }

    public function setup_polylang_strings() {
        if ( ! function_exists( 'pll_register_string' ) ) return;

        $strings = [
            'restaurant_name'  => get_option( 'nth_restaurant_name', 'Ngon Thị Hoa' ),
            'restaurant_desc'  => 'Nhà hàng ẩm thực Việt Nam đặc sắc tại Đà Nẵng',
            'opening_hours'    => get_option( 'nth_restaurant_hours', 'Hằng ngày: 6:30 - 22:00' ),
            'cta_reserve'      => 'Đặt Bàn',
            'cta_menu'         => 'Xem Thực Đơn',
            'nav_home'         => 'Trang Chủ',
            'nav_menu'         => 'Thực Đơn',
            'nav_blog'         => 'Tin Tức',
            'nav_media'        => 'Hình Ảnh',
            'nav_recruitment'  => 'Tuyển Dụng',
            'nav_contact'      => 'Liên Hệ',
        ];

        foreach ( $strings as $name => $string ) {
            pll_register_string( 'ngonthihoa-' . $name, $string, 'Ngon Thi Hoa' );
        }
    }

    /**
     * Get the current language code (works with WPML, Polylang, or standalone)
     */
    public static function get_current_language() {
        // WPML
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            return ICL_LANGUAGE_CODE;
        }
        // Polylang
        if ( function_exists( 'pll_current_language' ) ) {
            return pll_current_language();
        }
        // Standalone: read from cookie or browser
        if ( isset( $_COOKIE['nth_language'] ) && array_key_exists( $_COOKIE['nth_language'], self::$languages ) ) {
            return sanitize_key( $_COOKIE['nth_language'] );
        }
        return get_option( 'nth_default_language', 'vi' );
    }

    /**
     * Get translated meta field value
     * Falls back through: current_lang → vi fallback
     */
    public static function get_translated_meta( $post_id, $field_base, $lang = null ) {
        $lang = $lang ?: self::get_current_language();

        // WPML: use icl_object_id or custom field suffix
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            // WPML handles post translation natively; just get the post in current language
            $translated_id = apply_filters( 'wpml_object_id', $post_id, get_post_type( $post_id ), true );
            return get_post_meta( $translated_id, '_nth_' . $field_base, true );
        }

        // Polylang / standalone: try lang-suffixed field first
        if ( $lang !== 'vi' ) {
            $value = get_post_meta( $post_id, "_nth_{$field_base}_{$lang}", true );
            if ( $value ) return $value;
        }

        // Fallback to base (Vietnamese)
        return get_post_meta( $post_id, "_nth_{$field_base}", true );
    }

    public function inject_language_switcher_script() {
        if ( defined( 'ICL_LANGUAGE_CODE' ) || function_exists( 'pll_current_language' ) ) return;
        // Standalone language switcher — sets cookie and reloads
        ?>
        <script>
        (function() {
            var langs = <?php echo wp_json_encode( array_keys( self::$languages ) ); ?>;
            // Detect from browser if no cookie
            function detectLang() {
                var stored = document.cookie.match(/nth_language=([a-z]{2})/);
                if (stored && langs.indexOf(stored[1]) >= 0) return stored[1];
                var nav = navigator.language || navigator.userLanguage || '';
                var code = nav.slice(0,2).toLowerCase();
                if (code === 'zh') return 'zh';
                if (code === 'ko') return 'ko';
                if (code === 'en') return 'en';
                return 'vi';
            }
            window.nth_current_lang = detectLang();
            window.nth_set_language = function(code) {
                var d = new Date();
                d.setFullYear(d.getFullYear() + 1);
                document.cookie = 'nth_language=' + code + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
                location.reload();
            };
            document.querySelectorAll('[data-nth-lang]').forEach(function(el) {
                el.addEventListener('click', function() {
                    window.nth_set_language(this.getAttribute('data-nth-lang'));
                });
            });
        })();
        </script>
        <?php
    }

    public function add_multilingual_settings() {
        add_submenu_page(
            'nth_dashboard',
            __( 'Cài đặt đa ngôn ngữ', NTH_TEXT_DOMAIN ),
            __( 'Đa ngôn ngữ', NTH_TEXT_DOMAIN ),
            'manage_options',
            'nth_multilingual',
            [ $this, 'render_settings_page' ]
        );
    }

    public function register_settings() {
        register_setting( 'nth_multilingual_group', 'nth_default_language', [ 'sanitize_callback' => 'sanitize_key' ] );
        register_setting( 'nth_multilingual_group', 'nth_languages_enabled' );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Cài đặt đa ngôn ngữ', NTH_TEXT_DOMAIN ); ?></h1>

            <?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
            <div class="notice notice-success"><p><?php _e( 'WPML đã được kích hoạt. Quản lý ngôn ngữ qua menu WPML.', NTH_TEXT_DOMAIN ); ?></p></div>
            <?php elseif ( function_exists( 'pll_current_language' ) ) : ?>
            <div class="notice notice-success"><p><?php _e( 'Polylang đã được kích hoạt. Quản lý ngôn ngữ qua menu Polylang.', NTH_TEXT_DOMAIN ); ?></p></div>
            <?php else : ?>
            <div class="notice notice-warning">
                <p><strong><?php _e( 'Khuyến nghị:', NTH_TEXT_DOMAIN ); ?></strong> <?php _e( 'Cài thêm plugin Polylang (miễn phí) hoặc WPML để quản lý đa ngôn ngữ đầy đủ nhất.', NTH_TEXT_DOMAIN ); ?></p>
                <p>
                    <a href="<?php echo admin_url( 'plugin-install.php?s=polylang&tab=search&type=term' ); ?>" class="button"><?php _e( 'Cài Polylang', NTH_TEXT_DOMAIN ); ?></a>
                    <a href="https://wpml.org" target="_blank" class="button" style="margin-left:8px;"><?php _e( 'Mua WPML', NTH_TEXT_DOMAIN ); ?></a>
                </p>
            </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'nth_multilingual_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Ngôn ngữ mặc định', NTH_TEXT_DOMAIN ); ?></th>
                        <td>
                            <select name="nth_default_language">
                                <?php foreach ( self::$languages as $code => $info ) : ?>
                                <option value="<?php echo esc_attr( $code ); ?>" <?php selected( get_option( 'nth_default_language', 'vi' ), $code ); ?>>
                                    <?php echo esc_html( $info['flag'] . ' ' . $info['name'] ); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Ngôn ngữ được kích hoạt', NTH_TEXT_DOMAIN ); ?></th>
                        <td>
                            <?php
                            $enabled = (array) get_option( 'nth_languages_enabled', ['vi','en','zh','ko'] );
                            foreach ( self::$languages as $code => $info ) : ?>
                            <label style="margin-right:16px;">
                                <input type="checkbox" name="nth_languages_enabled[]"
                                    value="<?php echo esc_attr( $code ); ?>"
                                    <?php checked( in_array( $code, $enabled ) ); ?> />
                                <?php echo esc_html( $info['flag'] . ' ' . $info['name'] ); ?>
                            </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button( __( 'Lưu cài đặt', NTH_TEXT_DOMAIN ) ); ?>
            </form>

            <hr>
            <h2><?php _e( 'Hướng dẫn cài Polylang', NTH_TEXT_DOMAIN ); ?></h2>
            <ol>
                <li><?php _e( 'Vào Plugins → Thêm mới → Tìm "Polylang" → Cài đặt và Kích hoạt', NTH_TEXT_DOMAIN ); ?></li>
                <li><?php _e( 'Vào Languages → Thêm các ngôn ngữ: Tiếng Việt (vi), English (en), Chinese Simplified (zh), Korean (ko)', NTH_TEXT_DOMAIN ); ?></li>
                <li><?php _e( 'Tất cả bài đăng, trang và menu sẽ có tab ngôn ngữ để nhập bản dịch', NTH_TEXT_DOMAIN ); ?></li>
                <li><?php _e( 'Custom fields (giá, mô tả, tên món) đã được đăng ký với Polylang để dịch', NTH_TEXT_DOMAIN ); ?></li>
            </ol>
        </div>
        <?php
    }

    public function register_rest_routes() {
        register_rest_route( 'ngonthihoa/v1', '/languages', [
            'methods'             => 'GET',
            'callback'            => function() {
                return self::$languages;
            },
            'permission_callback' => '__return_true',
        ] );
    }
}

new NTH_Multilingual();
