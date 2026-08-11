<?php
/**
 * Analytics Dashboard for Ngon Thi Hoa
 * Custom dashboard widget + admin page with charts
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Analytics {

    public function __construct() {
        add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
        add_action( 'admin_menu', [ $this, 'add_analytics_page' ] );
        add_action( 'wp_ajax_nth_get_chart_data', [ $this, 'ajax_chart_data' ] );
        add_action( 'wp_ajax_nth_export_analytics', [ $this, 'ajax_export' ] );
    }

    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'nth_dashboard_widget',
            __( 'Ngon Thị Hoa - Tổng quan', NTH_TEXT_DOMAIN ),
            [ $this, 'render_dashboard_widget' ]
        );
    }

    public function render_dashboard_widget() {
        $stats = NTH_Custom_Tables::get_summary_stats();
        ?>
        <div class="nth-dashboard-widget">
            <div class="nth-stats-grid">
                <div class="nth-stat-card nth-stat-blue">
                    <span class="nth-stat-number"><?php echo esc_html( $stats['reservations_today'] ); ?></span>
                    <span class="nth-stat-label"><?php _e( 'Đặt bàn hôm nay', NTH_TEXT_DOMAIN ); ?></span>
                </div>
                <div class="nth-stat-card nth-stat-orange">
                    <span class="nth-stat-number"><?php echo esc_html( $stats['reservations_pending'] ); ?></span>
                    <span class="nth-stat-label"><?php _e( 'Chờ xác nhận', NTH_TEXT_DOMAIN ); ?></span>
                </div>
                <div class="nth-stat-card nth-stat-red">
                    <span class="nth-stat-number"><?php echo esc_html( $stats['contacts_unread'] ); ?></span>
                    <span class="nth-stat-label"><?php _e( 'Liên hệ chưa đọc', NTH_TEXT_DOMAIN ); ?></span>
                </div>
                <div class="nth-stat-card nth-stat-green">
                    <span class="nth-stat-number"><?php echo esc_html( $stats['applications_pending'] ); ?></span>
                    <span class="nth-stat-label"><?php _e( 'Đơn tuyển dụng mới', NTH_TEXT_DOMAIN ); ?></span>
                </div>
            </div>
            <div class="nth-quick-links">
                <a href="<?php echo admin_url( 'admin.php?page=nth_reservations' ); ?>" class="button button-primary">
                    <?php _e( 'Quản lý đặt bàn', NTH_TEXT_DOMAIN ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=nth_analytics' ); ?>" class="button">
                    <?php _e( 'Xem báo cáo chi tiết', NTH_TEXT_DOMAIN ); ?>
                </a>
            </div>
        </div>
        <?php
    }

    public function add_analytics_page() {
        add_submenu_page(
            'nth_dashboard',
            __( 'Phân tích & Báo cáo', NTH_TEXT_DOMAIN ),
            __( 'Phân tích', NTH_TEXT_DOMAIN ),
            'manage_options',
            'nth_analytics',
            [ $this, 'render_analytics_page' ]
        );
    }

    public function render_analytics_page() {
        $stats      = NTH_Custom_Tables::get_summary_stats();
        $daily_data = NTH_Custom_Tables::get_reservations_by_day( 30 );
        $chart_labels = [];
        $chart_counts = [];
        $chart_guests = [];
        foreach ( $daily_data as $row ) {
            $chart_labels[] = date_i18n( 'd/m', strtotime( $row->date ) );
            $chart_counts[] = (int) $row->count;
            $chart_guests[] = (int) $row->guests;
        }
        ?>
        <div class="wrap nth-analytics-page">
            <h1><?php _e( 'Phân tích & Báo cáo', NTH_TEXT_DOMAIN ); ?></h1>

            <!-- Summary Cards -->
            <div class="nth-analytics-grid">
                <div class="nth-analytics-card">
                    <div class="nth-analytics-icon nth-icon-blue">📅</div>
                    <div class="nth-analytics-info">
                        <div class="nth-analytics-number"><?php echo esc_html( $stats['reservations_month'] ); ?></div>
                        <div class="nth-analytics-label"><?php _e( 'Đặt bàn tháng này', NTH_TEXT_DOMAIN ); ?></div>
                    </div>
                </div>
                <div class="nth-analytics-card">
                    <div class="nth-analytics-icon nth-icon-green">👥</div>
                    <div class="nth-analytics-info">
                        <div class="nth-analytics-number"><?php echo esc_html( $stats['guests_this_month'] ?: 0 ); ?></div>
                        <div class="nth-analytics-label"><?php _e( 'Khách tháng này', NTH_TEXT_DOMAIN ); ?></div>
                    </div>
                </div>
                <div class="nth-analytics-card">
                    <div class="nth-analytics-icon nth-icon-orange">📨</div>
                    <div class="nth-analytics-info">
                        <div class="nth-analytics-number"><?php echo esc_html( $stats['contacts_total'] ); ?></div>
                        <div class="nth-analytics-label"><?php _e( 'Tổng liên hệ', NTH_TEXT_DOMAIN ); ?></div>
                    </div>
                </div>
                <div class="nth-analytics-card">
                    <div class="nth-analytics-icon nth-icon-purple">💼</div>
                    <div class="nth-analytics-info">
                        <div class="nth-analytics-number"><?php echo esc_html( $stats['applications_total'] ); ?></div>
                        <div class="nth-analytics-label"><?php _e( 'Tổng ứng tuyển', NTH_TEXT_DOMAIN ); ?></div>
                    </div>
                </div>
            </div>

            <!-- Chart -->
            <div class="nth-chart-container">
                <h2><?php _e( 'Đặt bàn 30 ngày gần nhất', NTH_TEXT_DOMAIN ); ?></h2>
                <canvas id="nth-reservations-chart" height="120"></canvas>
            </div>

            <!-- Export buttons -->
            <div class="nth-export-section">
                <h2><?php _e( 'Xuất dữ liệu', NTH_TEXT_DOMAIN ); ?></h2>
                <p><?php _e( 'Xuất dữ liệu sang định dạng CSV để phân tích hoặc backup.', NTH_TEXT_DOMAIN ); ?></p>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=nth_analytics&nth_export=reservations' ), 'nth_export_nonce' ); ?>" class="button button-primary">
                    <?php _e( 'Xuất đặt bàn (CSV)', NTH_TEXT_DOMAIN ); ?>
                </a>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=nth_analytics&nth_export=contacts' ), 'nth_export_nonce' ); ?>" class="button">
                    <?php _e( 'Xuất liên hệ (CSV)', NTH_TEXT_DOMAIN ); ?>
                </a>
                <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=nth_analytics&nth_export=applications' ), 'nth_export_nonce' ); ?>" class="button">
                    <?php _e( 'Xuất ứng tuyển (CSV)', NTH_TEXT_DOMAIN ); ?>
                </a>
            </div>

            <!-- GA4 note -->
            <?php $ga4_id = get_option( 'nth_ga4_id' ); ?>
            <?php if ( $ga4_id ) : ?>
            <div class="nth-ga4-note notice notice-info">
                <p>
                    <?php printf(
                        __( 'Google Analytics 4 đã được kết nối với ID: <strong>%s</strong>. Xem báo cáo chi tiết tại <a href="%s" target="_blank">Google Analytics</a>.', NTH_TEXT_DOMAIN ),
                        esc_html( $ga4_id ),
                        'https://analytics.google.com'
                    ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('nth-reservations-chart');
            if (!ctx || typeof Chart === 'undefined') return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode( $chart_labels ); ?>,
                    datasets: [
                        {
                            label: '<?php _e( 'Số đặt bàn', NTH_TEXT_DOMAIN ); ?>',
                            data: <?php echo json_encode( $chart_counts ); ?>,
                            backgroundColor: 'rgba(94,71,67,0.7)',
                            borderColor: '#5e4743',
                            borderWidth: 1
                        },
                        {
                            label: '<?php _e( 'Số khách', NTH_TEXT_DOMAIN ); ?>',
                            data: <?php echo json_encode( $chart_guests ); ?>,
                            backgroundColor: 'rgba(255,201,82,0.6)',
                            borderColor: '#ffc952',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        });
        </script>
        <?php

        // Handle CSV export
        if ( isset( $_GET['nth_export'] ) && check_admin_referer( 'nth_export_nonce' ) ) {
            $type = sanitize_key( $_GET['nth_export'] );
            NTH_Export::export_csv( $type );
            exit;
        }
    }

    public function ajax_chart_data() {
        check_ajax_referer( 'nth_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        $days = absint( $_POST['days'] ?? 30 );
        wp_send_json_success( NTH_Custom_Tables::get_reservations_by_day( $days ) );
    }

    public function ajax_export() {
        check_ajax_referer( 'nth_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        $type = sanitize_key( $_POST['type'] ?? 'reservations' );
        NTH_Export::export_csv( $type );
        exit;
    }
}

new NTH_Analytics();
