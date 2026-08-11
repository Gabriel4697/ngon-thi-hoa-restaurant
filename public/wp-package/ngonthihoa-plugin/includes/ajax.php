<?php
/**
 * Frontend AJAX handlers for Ngon Thi Hoa
 * Handles reservation, contact, and job application form submissions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Ajax {

    public function __construct() {
        // Both logged-in and non-logged-in users can submit forms
        $actions = [ 'nth_submit_reservation', 'nth_submit_contact', 'nth_submit_application' ];
        foreach ( $actions as $action ) {
            add_action( 'wp_ajax_' . $action,        [ $this, str_replace( 'nth_', 'handle_', $action ) ] );
            add_action( 'wp_ajax_nopriv_' . $action, [ $this, str_replace( 'nth_', 'handle_', $action ) ] );
        }
    }

    public function handle_submit_reservation() {
        // Nonce
        if ( ! check_ajax_referer( 'nth_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'message' => __( 'Yêu cầu không hợp lệ.', NTH_TEXT_DOMAIN ) ] );
        }

        // Honeypot
        if ( ! NTH_Security::check_honeypot( $_POST ) ) {
            wp_send_json_success( [ 'message' => __( 'Đặt bàn thành công!', NTH_TEXT_DOMAIN ) ] );
        }

        // Rate limit
        if ( ! NTH_Security::check_rate_limit( 'reservation' ) ) {
            wp_send_json_error( [ 'message' => __( 'Quá nhiều yêu cầu. Vui lòng thử lại sau.', NTH_TEXT_DOMAIN ) ] );
        }

        // reCAPTCHA
        if ( get_option( 'nth_security_recaptcha' ) && ! empty( $_POST['recaptcha_token'] ) ) {
            if ( ! NTH_Security::verify_recaptcha( sanitize_text_field( $_POST['recaptcha_token'] ) ) ) {
                wp_send_json_error( [ 'message' => __( 'Xác minh reCAPTCHA thất bại.', NTH_TEXT_DOMAIN ) ] );
            }
        }

        // Sanitize
        $data = NTH_Security::sanitize_form_data( $_POST, [
            'name'             => 'text',
            'email'            => 'email',
            'phone'            => 'phone',
            'reservation_date' => 'date',
            'reservation_time' => 'time',
            'number_of_guests' => 'int',
            'notes'            => 'textarea',
        ] );

        // Validate required
        $required = [ 'name', 'email', 'phone', 'reservation_date', 'reservation_time', 'number_of_guests' ];
        $valid    = NTH_Security::validate_required( $data, $required );
        if ( is_wp_error( $valid ) ) {
            wp_send_json_error( [ 'message' => $valid->get_error_message() ] );
        }

        // Validate email
        if ( ! is_email( $data['email'] ) ) {
            wp_send_json_error( [ 'message' => __( 'Email không hợp lệ.', NTH_TEXT_DOMAIN ) ] );
        }

        // Validate date is in the future
        if ( $data['reservation_date'] < date( 'Y-m-d' ) ) {
            wp_send_json_error( [ 'message' => __( 'Ngày đặt bàn phải là ngày trong tương lai.', NTH_TEXT_DOMAIN ) ] );
        }

        $data['status']   = 'pending';
        $data['language'] = sanitize_key( $_POST['language'] ?? 'vi' );
        $data['source']   = 'website';

        $result = NTH_Custom_Tables::insert_reservation( $data );

        if ( $result ) {
            $this->send_reservation_notification( $data );
            // Fire GA4 conversion event via cookie flag
            wp_send_json_success( [
                'message'      => __( 'Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.', NTH_TEXT_DOMAIN ),
                'track_event'  => 'reservation_submitted',
                'track_params' => [ 'date' => $data['reservation_date'], 'guests' => $data['number_of_guests'] ],
            ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'Đã có lỗi xảy ra. Vui lòng thử lại.', NTH_TEXT_DOMAIN ) ] );
        }
    }

    public function handle_submit_contact() {
        if ( ! check_ajax_referer( 'nth_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'message' => __( 'Yêu cầu không hợp lệ.', NTH_TEXT_DOMAIN ) ] );
        }
        if ( ! NTH_Security::check_honeypot( $_POST ) ) {
            wp_send_json_success( [ 'message' => __( 'Gửi thành công!', NTH_TEXT_DOMAIN ) ] );
        }
        if ( ! NTH_Security::check_rate_limit( 'contact' ) ) {
            wp_send_json_error( [ 'message' => __( 'Quá nhiều yêu cầu. Vui lòng thử lại sau.', NTH_TEXT_DOMAIN ) ] );
        }

        $data = NTH_Security::sanitize_form_data( $_POST, [
            'name'    => 'text',
            'email'   => 'email',
            'phone'   => 'phone',
            'subject' => 'text',
            'message' => 'textarea',
        ] );

        $valid = NTH_Security::validate_required( $data, ['name','email','message'] );
        if ( is_wp_error( $valid ) ) {
            wp_send_json_error( [ 'message' => $valid->get_error_message() ] );
        }
        if ( ! is_email( $data['email'] ) ) {
            wp_send_json_error( [ 'message' => __( 'Email không hợp lệ.', NTH_TEXT_DOMAIN ) ] );
        }

        $data['status']   = 'unread';
        $data['language'] = sanitize_key( $_POST['language'] ?? 'vi' );

        $result = NTH_Custom_Tables::insert_contact( $data );

        if ( $result ) {
            $this->send_contact_notification( $data );
            wp_send_json_success( [
                'message'     => __( 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi trong vòng 24 giờ.', NTH_TEXT_DOMAIN ),
                'track_event' => 'contact_submitted',
            ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'Đã có lỗi xảy ra. Vui lòng thử lại.', NTH_TEXT_DOMAIN ) ] );
        }
    }

    public function handle_submit_application() {
        if ( ! check_ajax_referer( 'nth_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'message' => __( 'Yêu cầu không hợp lệ.', NTH_TEXT_DOMAIN ) ] );
        }
        if ( ! NTH_Security::check_honeypot( $_POST ) ) {
            wp_send_json_success( [ 'message' => __( 'Gửi thành công!', NTH_TEXT_DOMAIN ) ] );
        }
        if ( ! NTH_Security::check_rate_limit( 'application' ) ) {
            wp_send_json_error( [ 'message' => __( 'Quá nhiều yêu cầu. Vui lòng thử lại sau.', NTH_TEXT_DOMAIN ) ] );
        }

        $data = NTH_Security::sanitize_form_data( $_POST, [
            'applicant_name'  => 'text',
            'applicant_email' => 'email',
            'applicant_phone' => 'phone',
            'job_id'          => 'int',
            'job_title'       => 'text',
            'cover_letter'    => 'textarea',
        ] );

        $valid = NTH_Security::validate_required( $data, ['applicant_name','applicant_email'] );
        if ( is_wp_error( $valid ) ) {
            wp_send_json_error( [ 'message' => $valid->get_error_message() ] );
        }

        // Handle CV file upload
        $cv_url = '';
        if ( ! empty( $_FILES['cv_file']['tmp_name'] ) ) {
            $valid_file = NTH_Security::validate_cv_upload( $_FILES['cv_file'] );
            if ( is_wp_error( $valid_file ) ) {
                wp_send_json_error( [ 'message' => $valid_file->get_error_message() ] );
            }
            $upload = wp_handle_upload( $_FILES['cv_file'], [ 'test_form' => false ] );
            if ( isset( $upload['error'] ) ) {
                wp_send_json_error( [ 'message' => __( 'Lỗi upload file: ', NTH_TEXT_DOMAIN ) . $upload['error'] ] );
            }
            $cv_url = $upload['url'] ?? '';
        }

        $data['cv_file']  = $cv_url;
        $data['status']   = 'pending';
        $data['language'] = sanitize_key( $_POST['language'] ?? 'vi' );

        $result = NTH_Custom_Tables::insert_application( $data );

        if ( $result ) {
            $this->send_application_notification( $data );
            wp_send_json_success( [
                'message'     => __( 'Đơn ứng tuyển đã được gửi thành công! Chúng tôi sẽ liên hệ với bạn.', NTH_TEXT_DOMAIN ),
                'track_event' => 'application_submitted',
            ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'Đã có lỗi xảy ra. Vui lòng thử lại.', NTH_TEXT_DOMAIN ) ] );
        }
    }

    private function send_reservation_notification( $data ) {
        $to      = get_option( 'nth_reservation_notify', get_option( 'admin_email' ) );
        $subject = sprintf( '[Ngon Thị Hoa] Đặt bàn mới từ %s - %s', $data['name'], $data['reservation_date'] );
        $body    = sprintf(
            "Thông tin đặt bàn mới:\n\nHọ tên: %s\nĐiện thoại: %s\nEmail: %s\nNgày: %s\nGiờ: %s\nSố khách: %s\nGhi chú: %s\n\nXem tại: %s",
            $data['name'], $data['phone'], $data['email'],
            $data['reservation_date'], $data['reservation_time'],
            $data['number_of_guests'], $data['notes'] ?? '',
            admin_url( 'admin.php?page=nth_reservations' )
        );
        wp_mail( $to, $subject, $body );
    }

    private function send_contact_notification( $data ) {
        $to      = get_option( 'nth_contact_notify', get_option( 'admin_email' ) );
        $subject = sprintf( '[Ngon Thị Hoa] Liên hệ mới từ %s', $data['name'] );
        $body    = sprintf(
            "Liên hệ mới:\n\nHọ tên: %s\nEmail: %s\nĐiện thoại: %s\nChủ đề: %s\nNội dung:\n%s\n\nXem tại: %s",
            $data['name'], $data['email'], $data['phone'] ?? '',
            $data['subject'] ?? '', $data['message'],
            admin_url( 'admin.php?page=nth_contacts' )
        );
        wp_mail( $to, $subject, $body );
    }

    private function send_application_notification( $data ) {
        $to      = get_option( 'nth_application_notify', get_option( 'admin_email' ) );
        $subject = sprintf( '[Ngon Thị Hoa] Đơn ứng tuyển mới từ %s - %s', $data['applicant_name'], $data['job_title'] ?? '' );
        $body    = sprintf(
            "Đơn ứng tuyển mới:\n\nỨng viên: %s\nEmail: %s\nĐiện thoại: %s\nVị trí: %s\nThư xin việc:\n%s\nCV: %s\n\nXem tại: %s",
            $data['applicant_name'], $data['applicant_email'], $data['applicant_phone'] ?? '',
            $data['job_title'] ?? '', $data['cover_letter'] ?? '',
            $data['cv_file'] ?? 'Không có',
            admin_url( 'admin.php?page=nth_applications' )
        );
        wp_mail( $to, $subject, $body );
    }
}

new NTH_Ajax();
