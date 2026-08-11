<?php
/**
 * Security hardening for Ngon Thi Hoa
 * Rate limiting, reCAPTCHA, input sanitization, nonce verification
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Security {

    private static $rate_limit_window = 3600; // 1 hour
    private static $max_attempts      = 10;

    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        // Add security headers
        add_action( 'send_headers', [ $this, 'send_security_headers' ] );
        // Honeypot field to forms
        add_action( 'nth_before_form_close', [ $this, 'add_honeypot_field' ] );
    }

    /**
     * Check rate limit for form submissions
     * Returns true if allowed, false if rate limited
     */
    public static function check_rate_limit( $action ) {
        $ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'nth_rl_' . md5( $ip . $action );

        $attempts = (int) get_transient( $key );
        if ( $attempts >= self::$max_attempts ) {
            return false;
        }

        set_transient( $key, $attempts + 1, self::$rate_limit_window );
        return true;
    }

    /**
     * Verify reCAPTCHA v3 token (if enabled)
     */
    public static function verify_recaptcha( $token ) {
        if ( ! get_option( 'nth_security_recaptcha' ) ) return true;

        $secret = get_option( 'nth_recaptcha_secret' );
        if ( ! $secret ) return true;

        $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ],
        ] );

        if ( is_wp_error( $response ) ) return false;

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        return ! empty( $body['success'] ) && ( $body['score'] ?? 0 ) >= 0.5;
    }

    /**
     * Sanitize form data array
     */
    public static function sanitize_form_data( $data, $fields ) {
        $sanitized = [];
        foreach ( $fields as $field => $type ) {
            if ( ! isset( $data[ $field ] ) ) continue;
            switch ( $type ) {
                case 'text':
                    $sanitized[ $field ] = sanitize_text_field( $data[ $field ] );
                    break;
                case 'email':
                    $sanitized[ $field ] = sanitize_email( $data[ $field ] );
                    break;
                case 'textarea':
                    $sanitized[ $field ] = sanitize_textarea_field( $data[ $field ] );
                    break;
                case 'int':
                    $sanitized[ $field ] = absint( $data[ $field ] );
                    break;
                case 'date':
                    $sanitized[ $field ] = sanitize_text_field( $data[ $field ] );
                    // Validate date format
                    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $sanitized[ $field ] ) ) {
                        $sanitized[ $field ] = '';
                    }
                    break;
                case 'time':
                    $sanitized[ $field ] = sanitize_text_field( $data[ $field ] );
                    if ( ! preg_match( '/^\d{2}:\d{2}(:\d{2})?$/', $sanitized[ $field ] ) ) {
                        $sanitized[ $field ] = '';
                    }
                    break;
                case 'phone':
                    $sanitized[ $field ] = preg_replace( '/[^\d\s\+\-\(\)]/', '', $data[ $field ] );
                    $sanitized[ $field ] = substr( $sanitized[ $field ], 0, 30 );
                    break;
                case 'url':
                    $sanitized[ $field ] = esc_url_raw( $data[ $field ] );
                    break;
            }
        }
        return $sanitized;
    }

    /**
     * Validate required fields
     */
    public static function validate_required( $data, $required_fields ) {
        foreach ( $required_fields as $field ) {
            if ( empty( $data[ $field ] ) ) {
                return new WP_Error( 'required_field', sprintf( __( 'Trường "%s" là bắt buộc.', NTH_TEXT_DOMAIN ), $field ) );
            }
        }
        return true;
    }

    /**
     * Check honeypot field (bot detection)
     */
    public static function check_honeypot( $post_data ) {
        // If honeypot field is filled, it's a bot
        return empty( $post_data['nth_hp_field'] );
    }

    public function send_security_headers() {
        if ( is_admin() ) return;
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-XSS-Protection: 1; mode=block' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    }

    public function add_honeypot_field() {
        echo '<div style="display:none;position:absolute;left:-9999px;" aria-hidden="true"><input type="text" name="nth_hp_field" tabindex="-1" autocomplete="off" /></div>';
    }

    /**
     * Validate file upload (for CV uploads)
     */
    public static function validate_cv_upload( $file ) {
        $allowed_types = [ 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ];
        $max_size      = 5 * 1024 * 1024; // 5 MB

        if ( $file['size'] > $max_size ) {
            return new WP_Error( 'file_too_large', __( 'File quá lớn. Tối đa 5MB.', NTH_TEXT_DOMAIN ) );
        }

        $finfo     = new finfo( FILEINFO_MIME_TYPE );
        $mime_type = $finfo->file( $file['tmp_name'] );

        if ( ! in_array( $mime_type, $allowed_types, true ) ) {
            return new WP_Error( 'invalid_file_type', __( 'Chỉ chấp nhận file PDF, DOC, DOCX.', NTH_TEXT_DOMAIN ) );
        }

        return true;
    }

    public function register_settings() {
        register_setting( 'nth_security_group', 'nth_security_recaptcha', [ 'sanitize_callback' => 'absint' ] );
        register_setting( 'nth_security_group', 'nth_recaptcha_site_key', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'nth_security_group', 'nth_recaptcha_secret', [ 'sanitize_callback' => 'sanitize_text_field' ] );
    }

    public function add_settings_page() {
        add_submenu_page(
            'nth_dashboard',
            __( 'Bảo mật', NTH_TEXT_DOMAIN ),
            __( 'Bảo mật', NTH_TEXT_DOMAIN ),
            'manage_options',
            'nth_security',
            [ $this, 'render_settings_page' ]
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Cài đặt bảo mật', NTH_TEXT_DOMAIN ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'nth_security_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Google reCAPTCHA v3', NTH_TEXT_DOMAIN ); ?></th>
                        <td>
                            <label><input type="checkbox" name="nth_security_recaptcha" value="1" <?php checked( get_option( 'nth_security_recaptcha' ), 1 ); ?> /> <?php _e( 'Bật reCAPTCHA v3 cho forms', NTH_TEXT_DOMAIN ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'reCAPTCHA Site Key', NTH_TEXT_DOMAIN ); ?></th>
                        <td>
                            <input type="text" name="nth_recaptcha_site_key" value="<?php echo esc_attr( get_option( 'nth_recaptcha_site_key' ) ); ?>" class="regular-text" />
                            <p class="description"><a href="https://www.google.com/recaptcha/admin" target="_blank"><?php _e( 'Lấy key tại Google reCAPTCHA Admin', NTH_TEXT_DOMAIN ); ?></a></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'reCAPTCHA Secret Key', NTH_TEXT_DOMAIN ); ?></th>
                        <td><input type="password" name="nth_recaptcha_secret" value="<?php echo esc_attr( get_option( 'nth_recaptcha_secret' ) ); ?>" class="regular-text" /></td>
                    </tr>
                </table>
                <h2><?php _e( 'Giới hạn gửi form', NTH_TEXT_DOMAIN ); ?></h2>
                <p><?php printf( __( 'Mỗi IP chỉ được gửi tối đa %d lần/giờ. Honeypot bot detection được bật tự động.', NTH_TEXT_DOMAIN ), self::$max_attempts ); ?></p>
                <?php submit_button( __( 'Lưu cài đặt', NTH_TEXT_DOMAIN ) ); ?>
            </form>
        </div>
        <?php
    }
}

new NTH_Security();
