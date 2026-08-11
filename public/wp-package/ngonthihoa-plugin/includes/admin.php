<?php
/**
 * Admin dashboard for Ngon Thi Hoa
 * Main menu, reservations, contacts, job applications management
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menus' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_notices', [ $this, 'show_pending_notices' ] );
        add_filter( 'set-screen-option', [ $this, 'set_screen_options' ], 10, 3 );

        // AJAX handlers
        add_action( 'wp_ajax_nth_update_reservation_status', [ $this, 'ajax_update_reservation' ] );
        add_action( 'wp_ajax_nth_update_contact_status',     [ $this, 'ajax_update_contact' ] );
        add_action( 'wp_ajax_nth_update_application_status', [ $this, 'ajax_update_application' ] );
        add_action( 'wp_ajax_nth_delete_record',             [ $this, 'ajax_delete_record' ] );
    }

    public function register_menus() {
        add_menu_page(
            __( 'Ngon Thị Hoa', NTH_TEXT_DOMAIN ),
            __( 'Ngon Thị Hoa', NTH_TEXT_DOMAIN ),
            'manage_options',
            'nth_dashboard',
            [ $this, 'render_dashboard' ],
            'dashicons-food',
            25
        );

        add_submenu_page( 'nth_dashboard', __( 'Tổng quan', NTH_TEXT_DOMAIN ), __( 'Tổng quan', NTH_TEXT_DOMAIN ), 'manage_options', 'nth_dashboard', [ $this, 'render_dashboard' ] );
        add_submenu_page( 'nth_dashboard', __( 'Đặt bàn', NTH_TEXT_DOMAIN ), __( 'Đặt bàn', NTH_TEXT_DOMAIN ), 'manage_options', 'nth_reservations', [ $this, 'render_reservations' ] );
        add_submenu_page( 'nth_dashboard', __( 'Liên hệ', NTH_TEXT_DOMAIN ), __( 'Liên hệ', NTH_TEXT_DOMAIN ), 'manage_options', 'nth_contacts', [ $this, 'render_contacts' ] );
        add_submenu_page( 'nth_dashboard', __( 'Tuyển dụng', NTH_TEXT_DOMAIN ), __( 'Tuyển dụng', NTH_TEXT_DOMAIN ), 'manage_options', 'nth_applications', [ $this, 'render_applications' ] );
        add_submenu_page( 'nth_dashboard', __( 'Cài đặt nhà hàng', NTH_TEXT_DOMAIN ), __( 'Cài đặt', NTH_TEXT_DOMAIN ), 'manage_options', 'nth_settings', [ $this, 'render_settings' ] );
    }

    public function register_settings() {
        $restaurant_fields = [
            'nth_restaurant_name', 'nth_restaurant_phone', 'nth_restaurant_phone2',
            'nth_restaurant_phone3', 'nth_restaurant_email', 'nth_restaurant_address',
            'nth_restaurant_hours', 'nth_facebook_url', 'nth_tripadvisor_url',
            'nth_google_maps_url', 'nth_google_maps_embed',
            'nth_reservation_notify', 'nth_contact_notify', 'nth_application_notify',
        ];
        foreach ( $restaurant_fields as $field ) {
            register_setting( 'nth_restaurant_group', $field, [ 'sanitize_callback' => 'sanitize_text_field' ] );
        }
    }

    public function show_pending_notices() {
        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'nth_' ) === false ) return;

        $stats = NTH_Custom_Tables::get_summary_stats();
        if ( $stats['reservations_pending'] > 0 ) {
            printf(
                '<div class="notice notice-warning"><p><strong>%d</strong> %s — <a href="%s">%s</a></p></div>',
                $stats['reservations_pending'],
                __( 'đặt bàn đang chờ xác nhận', NTH_TEXT_DOMAIN ),
                admin_url( 'admin.php?page=nth_reservations&status=pending' ),
                __( 'Xem ngay', NTH_TEXT_DOMAIN )
            );
        }
        if ( $stats['contacts_unread'] > 0 ) {
            printf(
                '<div class="notice notice-info"><p><strong>%d</strong> %s — <a href="%s">%s</a></p></div>',
                $stats['contacts_unread'],
                __( 'liên hệ chưa đọc', NTH_TEXT_DOMAIN ),
                admin_url( 'admin.php?page=nth_contacts&status=unread' ),
                __( 'Xem ngay', NTH_TEXT_DOMAIN )
            );
        }
    }

    public function set_screen_options( $status, $option, $value ) {
        return $value;
    }

    public function render_dashboard() {
        $stats = NTH_Custom_Tables::get_summary_stats();
        ?>
        <div class="wrap nth-admin-dashboard">
            <h1><?php _e( 'Ngon Thị Hoa — Bảng điều khiển', NTH_TEXT_DOMAIN ); ?></h1>
            <div class="nth-stats-grid">
                <div class="nth-stat-card" style="--nth-card-color:#0ea5e9;">
                    <div class="nth-stat-icon">📅</div>
                    <div class="nth-stat-number"><?php echo esc_html( $stats['reservations_today'] ); ?></div>
                    <div class="nth-stat-label"><?php _e( 'Đặt bàn hôm nay', NTH_TEXT_DOMAIN ); ?></div>
                    <a href="<?php echo admin_url( 'admin.php?page=nth_reservations' ); ?>" class="nth-stat-link"><?php _e( 'Xem tất cả', NTH_TEXT_DOMAIN ); ?></a>
                </div>
                <div class="nth-stat-card" style="--nth-card-color:#f59e0b;">
                    <div class="nth-stat-icon">⏳</div>
                    <div class="nth-stat-number"><?php echo esc_html( $stats['reservations_pending'] ); ?></div>
                    <div class="nth-stat-label"><?php _e( 'Chờ xác nhận', NTH_TEXT_DOMAIN ); ?></div>
                    <a href="<?php echo admin_url( 'admin.php?page=nth_reservations&status=pending' ); ?>" class="nth-stat-link"><?php _e( 'Xử lý', NTH_TEXT_DOMAIN ); ?></a>
                </div>
                <div class="nth-stat-card" style="--nth-card-color:#ef4444;">
                    <div class="nth-stat-icon">✉️</div>
                    <div class="nth-stat-number"><?php echo esc_html( $stats['contacts_unread'] ); ?></div>
                    <div class="nth-stat-label"><?php _e( 'Liên hệ chưa đọc', NTH_TEXT_DOMAIN ); ?></div>
                    <a href="<?php echo admin_url( 'admin.php?page=nth_contacts&status=unread' ); ?>" class="nth-stat-link"><?php _e( 'Xem', NTH_TEXT_DOMAIN ); ?></a>
                </div>
                <div class="nth-stat-card" style="--nth-card-color:#8b5cf6;">
                    <div class="nth-stat-icon">💼</div>
                    <div class="nth-stat-number"><?php echo esc_html( $stats['applications_pending'] ); ?></div>
                    <div class="nth-stat-label"><?php _e( 'Đơn ứng tuyển mới', NTH_TEXT_DOMAIN ); ?></div>
                    <a href="<?php echo admin_url( 'admin.php?page=nth_applications&status=pending' ); ?>" class="nth-stat-link"><?php _e( 'Xem', NTH_TEXT_DOMAIN ); ?></a>
                </div>
                <div class="nth-stat-card" style="--nth-card-color:#10b981;">
                    <div class="nth-stat-icon">👥</div>
                    <div class="nth-stat-number"><?php echo esc_html( $stats['guests_this_month'] ?: 0 ); ?></div>
                    <div class="nth-stat-label"><?php _e( 'Khách tháng này', NTH_TEXT_DOMAIN ); ?></div>
                </div>
                <div class="nth-stat-card" style="--nth-card-color:#5e4743;">
                    <div class="nth-stat-icon">🍽️</div>
                    <div class="nth-stat-number"><?php echo esc_html( $stats['reservations_month'] ); ?></div>
                    <div class="nth-stat-label"><?php _e( 'Đặt bàn tháng này', NTH_TEXT_DOMAIN ); ?></div>
                </div>
            </div>
            <div class="nth-quick-actions">
                <h2><?php _e( 'Thao tác nhanh', NTH_TEXT_DOMAIN ); ?></h2>
                <a href="<?php echo admin_url( 'post-new.php?post_type=menu_item' ); ?>" class="button button-primary"><?php _e( 'Thêm món ăn', NTH_TEXT_DOMAIN ); ?></a>
                <a href="<?php echo admin_url( 'post-new.php?post_type=blog_post' ); ?>" class="button"><?php _e( 'Thêm bài viết', NTH_TEXT_DOMAIN ); ?></a>
                <a href="<?php echo admin_url( 'post-new.php?post_type=job_position' ); ?>" class="button"><?php _e( 'Thêm tin tuyển dụng', NTH_TEXT_DOMAIN ); ?></a>
                <a href="<?php echo admin_url( 'admin.php?page=nth_analytics' ); ?>" class="button"><?php _e( 'Xem báo cáo', NTH_TEXT_DOMAIN ); ?></a>
                <a href="<?php echo admin_url( 'admin.php?page=nth_seo_settings' ); ?>" class="button"><?php _e( 'Cài đặt SEO & Tracking', NTH_TEXT_DOMAIN ); ?></a>
            </div>
        </div>
        <?php
    }

    public function render_reservations() {
        $status   = sanitize_key( $_GET['status'] ?? '' );
        $search   = sanitize_text_field( $_GET['search'] ?? '' );
        $page     = max( 1, absint( $_GET['paged'] ?? 1 ) );
        $per_page = 20;

        $args = [ 'status' => $status, 'search' => $search, 'per_page' => $per_page, 'page' => $page ];
        $rows = NTH_Custom_Tables::get_reservations( $args );
        $total = NTH_Custom_Tables::count_reservations( $args );
        $statuses = [ '' => __('Tất cả',NTH_TEXT_DOMAIN), 'pending'=>__('Chờ xác nhận',NTH_TEXT_DOMAIN), 'confirmed'=>__('Đã xác nhận',NTH_TEXT_DOMAIN), 'cancelled'=>__('Đã hủy',NTH_TEXT_DOMAIN), 'completed'=>__('Hoàn thành',NTH_TEXT_DOMAIN) ];
        ?>
        <div class="wrap">
            <h1><?php _e( 'Quản lý đặt bàn', NTH_TEXT_DOMAIN ); ?>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=nth_analytics&nth_export=reservations' ), 'nth_export_nonce' ); ?>" class="page-title-action"><?php _e( 'Xuất CSV', NTH_TEXT_DOMAIN ); ?></a>
            </h1>

            <!-- Status filters -->
            <ul class="subsubsub">
                <?php foreach ( $statuses as $key => $label ) : ?>
                <li><a href="<?php echo admin_url( 'admin.php?page=nth_reservations' . ( $key ? '&status=' . $key : '' ) ); ?>"
                       class="<?php echo $status === $key ? 'current' : ''; ?>"><?php echo esc_html( $label ); ?></a> |</li>
                <?php endforeach; ?>
            </ul>

            <!-- Search -->
            <form method="get" action="">
                <input type="hidden" name="page" value="nth_reservations" />
                <p class="search-box">
                    <input type="search" name="search" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php _e( 'Tìm tên, email, số điện thoại...', NTH_TEXT_DOMAIN ); ?>" />
                    <button type="submit" class="button"><?php _e( 'Tìm kiếm', NTH_TEXT_DOMAIN ); ?></button>
                </p>
            </form>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('ID',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Họ tên',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Điện thoại',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Email',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Ngày đặt',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Giờ',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Khách',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Trạng thái',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Ngày tạo',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Thao tác',NTH_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $rows ) ) : ?>
                    <tr><td colspan="10"><?php _e( 'Chưa có đặt bàn nào.', NTH_TEXT_DOMAIN ); ?></td></tr>
                    <?php else : ?>
                    <?php foreach ( $rows as $row ) : ?>
                    <tr data-id="<?php echo esc_attr( $row->id ); ?>">
                        <td><?php echo esc_html( $row->id ); ?></td>
                        <td><strong><?php echo esc_html( $row->name ); ?></strong>
                            <?php if ( $row->notes ) : ?>
                            <br><small><?php echo esc_html( wp_trim_words( $row->notes, 8 ) ); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( $row->phone ); ?></td>
                        <td><?php echo esc_html( $row->email ); ?></td>
                        <td><?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $row->reservation_date ) ) ); ?></td>
                        <td><?php echo esc_html( substr( $row->reservation_time, 0, 5 ) ); ?></td>
                        <td><?php echo esc_html( $row->number_of_guests ); ?></td>
                        <td>
                            <select class="nth-status-select" data-id="<?php echo esc_attr( $row->id ); ?>" data-type="reservation">
                                <?php foreach ( $statuses as $key => $label ) : ?>
                                <?php if ( ! $key ) continue; ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $row->status, $key ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $row->created_at ) ) ); ?></td>
                        <td>
                            <button class="button button-small nth-delete-btn" data-id="<?php echo esc_attr( $row->id ); ?>" data-type="reservation"><?php _e('Xóa',NTH_TEXT_DOMAIN); ?></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php $this->render_pagination( $total, $per_page, $page, 'nth_reservations', $status, $search ); ?>
        </div>
        <?php
    }

    public function render_contacts() {
        $status   = sanitize_key( $_GET['status'] ?? '' );
        $search   = sanitize_text_field( $_GET['search'] ?? '' );
        $page     = max( 1, absint( $_GET['paged'] ?? 1 ) );
        $rows     = NTH_Custom_Tables::get_contacts( [ 'status' => $status, 'search' => $search, 'page' => $page ] );
        ?>
        <div class="wrap">
            <h1><?php _e( 'Quản lý liên hệ', NTH_TEXT_DOMAIN ); ?></h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('ID',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Họ tên',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Email',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Điện thoại',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Chủ đề',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Nội dung',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Trạng thái',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Ngày gửi',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Thao tác',NTH_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $rows ) ) : ?>
                    <tr><td colspan="9"><?php _e( 'Chưa có liên hệ nào.', NTH_TEXT_DOMAIN ); ?></td></tr>
                    <?php else : ?>
                    <?php foreach ( $rows as $row ) : ?>
                    <tr>
                        <td><?php echo esc_html( $row->id ); ?></td>
                        <td><strong><?php echo esc_html( $row->name ); ?></strong></td>
                        <td><?php echo esc_html( $row->email ); ?></td>
                        <td><?php echo esc_html( $row->phone ); ?></td>
                        <td><?php echo esc_html( $row->subject ); ?></td>
                        <td><?php echo esc_html( wp_trim_words( $row->message, 15 ) ); ?></td>
                        <td>
                            <span class="nth-badge nth-badge-<?php echo esc_attr( $row->status ); ?>">
                                <?php echo $row->status === 'unread' ? __('Chưa đọc',NTH_TEXT_DOMAIN) : __('Đã đọc',NTH_TEXT_DOMAIN); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $row->created_at ) ) ); ?></td>
                        <td>
                            <button class="button button-small nth-mark-read-btn" data-id="<?php echo esc_attr( $row->id ); ?>"><?php _e('Đánh dấu đã đọc',NTH_TEXT_DOMAIN); ?></button>
                            <button class="button button-small nth-delete-btn" data-id="<?php echo esc_attr( $row->id ); ?>" data-type="contact" style="margin-top:4px;"><?php _e('Xóa',NTH_TEXT_DOMAIN); ?></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function render_applications() {
        $status = sanitize_key( $_GET['status'] ?? '' );
        $page   = max( 1, absint( $_GET['paged'] ?? 1 ) );
        $rows   = NTH_Custom_Tables::get_applications( [ 'status' => $status, 'page' => $page ] );
        $statuses = [ 'pending'=>__('Chờ xét',NTH_TEXT_DOMAIN), 'reviewing'=>__('Đang xét',NTH_TEXT_DOMAIN), 'interview'=>__('Phỏng vấn',NTH_TEXT_DOMAIN), 'accepted'=>__('Nhận việc',NTH_TEXT_DOMAIN), 'rejected'=>__('Từ chối',NTH_TEXT_DOMAIN) ];
        ?>
        <div class="wrap">
            <h1><?php _e( 'Quản lý ứng tuyển', NTH_TEXT_DOMAIN ); ?></h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('ID',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Ứng viên',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Email',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Điện thoại',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Vị trí',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('CV',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Trạng thái',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Ngày gửi',NTH_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Thao tác',NTH_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $rows ) ) : ?>
                    <tr><td colspan="9"><?php _e( 'Chưa có đơn ứng tuyển nào.', NTH_TEXT_DOMAIN ); ?></td></tr>
                    <?php else : ?>
                    <?php foreach ( $rows as $row ) : ?>
                    <tr>
                        <td><?php echo esc_html( $row->id ); ?></td>
                        <td><strong><?php echo esc_html( $row->applicant_name ); ?></strong></td>
                        <td><?php echo esc_html( $row->applicant_email ); ?></td>
                        <td><?php echo esc_html( $row->applicant_phone ); ?></td>
                        <td><?php echo esc_html( $row->job_title ); ?></td>
                        <td>
                            <?php if ( $row->cv_file ) : ?>
                            <a href="<?php echo esc_url( $row->cv_file ); ?>" target="_blank" class="button button-small"><?php _e('Tải CV',NTH_TEXT_DOMAIN); ?></a>
                            <?php else : ?>
                            <span>—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <select class="nth-status-select" data-id="<?php echo esc_attr( $row->id ); ?>" data-type="application">
                                <?php foreach ( $statuses as $key => $label ) : ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $row->status, $key ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $row->created_at ) ) ); ?></td>
                        <td>
                            <button class="button button-small nth-delete-btn" data-id="<?php echo esc_attr( $row->id ); ?>" data-type="application"><?php _e('Xóa',NTH_TEXT_DOMAIN); ?></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function render_settings() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Cài đặt nhà hàng', NTH_TEXT_DOMAIN ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'nth_restaurant_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Tên nhà hàng',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="text" name="nth_restaurant_name" value="<?php echo esc_attr(get_option('nth_restaurant_name')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Số điện thoại 1',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="text" name="nth_restaurant_phone" value="<?php echo esc_attr(get_option('nth_restaurant_phone')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Số điện thoại 2',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="text" name="nth_restaurant_phone2" value="<?php echo esc_attr(get_option('nth_restaurant_phone2')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Số điện thoại 3',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="text" name="nth_restaurant_phone3" value="<?php echo esc_attr(get_option('nth_restaurant_phone3')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Email',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="email" name="nth_restaurant_email" value="<?php echo esc_attr(get_option('nth_restaurant_email')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Địa chỉ',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="text" name="nth_restaurant_address" value="<?php echo esc_attr(get_option('nth_restaurant_address')); ?>" class="large-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Giờ mở cửa',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="text" name="nth_restaurant_hours" value="<?php echo esc_attr(get_option('nth_restaurant_hours','Hằng ngày / Daily: 6:30 - 22:00')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Link Facebook',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="url" name="nth_facebook_url" value="<?php echo esc_url(get_option('nth_facebook_url')); ?>" class="large-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Link Tripadvisor',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="url" name="nth_tripadvisor_url" value="<?php echo esc_url(get_option('nth_tripadvisor_url')); ?>" class="large-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Google Maps URL',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="url" name="nth_google_maps_url" value="<?php echo esc_url(get_option('nth_google_maps_url')); ?>" class="large-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Google Maps Embed URL',NTH_TEXT_DOMAIN); ?></th>
                        <td>
                            <input type="url" name="nth_google_maps_embed" value="<?php echo esc_url(get_option('nth_google_maps_embed')); ?>" class="large-text" />
                            <p class="description"><?php _e('URL dạng https://www.google.com/maps/embed?pb=... để nhúng bản đồ vào website',NTH_TEXT_DOMAIN); ?></p>
                        </td>
                    </tr>
                    <tr><th colspan="2"><h2><?php _e('Email thông báo',NTH_TEXT_DOMAIN); ?></h2></th></tr>
                    <tr>
                        <th><?php _e('Email nhận đặt bàn',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="email" name="nth_reservation_notify" value="<?php echo esc_attr(get_option('nth_reservation_notify',get_option('admin_email'))); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Email nhận liên hệ',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="email" name="nth_contact_notify" value="<?php echo esc_attr(get_option('nth_contact_notify',get_option('admin_email'))); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Email nhận đơn tuyển dụng',NTH_TEXT_DOMAIN); ?></th>
                        <td><input type="email" name="nth_application_notify" value="<?php echo esc_attr(get_option('nth_application_notify',get_option('admin_email'))); ?>" class="regular-text" /></td>
                    </tr>
                </table>
                <?php submit_button( __('Lưu cài đặt',NTH_TEXT_DOMAIN) ); ?>
            </form>
        </div>
        <?php
    }

    private function render_pagination( $total, $per_page, $current_page, $menu_slug, $status, $search ) {
        $total_pages = ceil( $total / $per_page );
        if ( $total_pages <= 1 ) return;
        echo '<div class="tablenav"><div class="tablenav-pages">';
        echo paginate_links( [
            'base'    => admin_url( 'admin.php?page=' . $menu_slug . '%_%' ),
            'format'  => '&paged=%#%',
            'current' => $current_page,
            'total'   => $total_pages,
        ] );
        echo '</div></div>';
    }

    // AJAX handlers
    public function ajax_update_reservation() {
        check_ajax_referer( 'nth_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        $id     = absint( $_POST['id'] );
        $status = sanitize_key( $_POST['status'] );
        $notes  = sanitize_textarea_field( $_POST['notes'] ?? '' );
        NTH_Custom_Tables::update_reservation_status( $id, $status, $notes );
        wp_send_json_success();
    }

    public function ajax_update_contact() {
        check_ajax_referer( 'nth_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        NTH_Custom_Tables::mark_contact_read( absint( $_POST['id'] ) );
        wp_send_json_success();
    }

    public function ajax_update_application() {
        check_ajax_referer( 'nth_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        NTH_Custom_Tables::update_application_status( absint( $_POST['id'] ), sanitize_key( $_POST['status'] ), sanitize_textarea_field( $_POST['notes'] ?? '' ) );
        wp_send_json_success();
    }

    public function ajax_delete_record() {
        check_ajax_referer( 'nth_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        $id   = absint( $_POST['id'] );
        $type = sanitize_key( $_POST['type'] );
        switch ( $type ) {
            case 'reservation': NTH_Custom_Tables::delete_reservation( $id ); break;
            case 'contact':     NTH_Custom_Tables::delete_contact( $id ); break;
            case 'application': NTH_Custom_Tables::delete_application( $id ); break;
        }
        wp_send_json_success();
    }
}

new NTH_Admin();
