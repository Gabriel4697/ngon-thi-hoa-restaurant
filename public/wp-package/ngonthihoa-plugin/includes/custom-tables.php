<?php
/**
 * Custom database table queries for Ngon Thi Hoa
 * Reservations, Contact Submissions, Job Applications
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Custom_Tables {

    // ── RESERVATIONS ──────────────────────────────────────────────────────────

    public static function get_reservations( $args = [] ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nth_reservations';
        $defaults = [
            'status'    => '',
            'date_from' => '',
            'date_to'   => '',
            'search'    => '',
            'orderby'   => 'reservation_date',
            'order'     => 'DESC',
            'per_page'  => 20,
            'page'      => 1,
        ];
        $args  = wp_parse_args( $args, $defaults );
        $where = [ '1=1' ];
        $values = [];

        if ( $args['status'] ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }
        if ( $args['date_from'] ) {
            $where[]  = 'reservation_date >= %s';
            $values[] = $args['date_from'];
        }
        if ( $args['date_to'] ) {
            $where[]  = 'reservation_date <= %s';
            $values[] = $args['date_to'];
        }
        if ( $args['search'] ) {
            $where[]  = '(name LIKE %s OR email LIKE %s OR phone LIKE %s)';
            $like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $values[] = $like;
            $values[] = $like;
            $values[] = $like;
        }

        $where_sql = implode( ' AND ', $where );
        $order_sql = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] ) ?: 'reservation_date DESC';
        $offset    = ( absint( $args['page'] ) - 1 ) * absint( $args['per_page'] );

        $query = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$order_sql} LIMIT %d OFFSET %d";
        $values[] = absint( $args['per_page'] );
        $values[] = $offset;

        if ( $values ) {
            return $wpdb->get_results( $wpdb->prepare( $query, $values ) );
        }
        return $wpdb->get_results( $query );
    }

    public static function count_reservations( $args = [] ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nth_reservations';
        $where = [ '1=1' ];
        $values = [];
        if ( ! empty( $args['status'] ) ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }
        $where_sql = implode( ' AND ', $where );
        $query = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        if ( $values ) {
            return (int) $wpdb->get_var( $wpdb->prepare( $query, $values ) );
        }
        return (int) $wpdb->get_var( $query );
    }

    public static function insert_reservation( $data ) {
        global $wpdb;
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $data['created_at'] = current_time( 'mysql' );
        $data['updated_at'] = current_time( 'mysql' );
        return $wpdb->insert( $wpdb->prefix . 'nth_reservations', $data );
    }

    public static function update_reservation_status( $id, $status, $admin_notes = '' ) {
        global $wpdb;
        return $wpdb->update(
            $wpdb->prefix . 'nth_reservations',
            [ 'status' => sanitize_text_field( $status ), 'admin_notes' => sanitize_textarea_field( $admin_notes ), 'updated_at' => current_time( 'mysql' ) ],
            [ 'id' => absint( $id ) ]
        );
    }

    public static function delete_reservation( $id ) {
        global $wpdb;
        return $wpdb->delete( $wpdb->prefix . 'nth_reservations', [ 'id' => absint( $id ) ] );
    }

    // ── CONTACTS ──────────────────────────────────────────────────────────────

    public static function get_contacts( $args = [] ) {
        global $wpdb;
        $table    = $wpdb->prefix . 'nth_contacts';
        $defaults = [ 'status' => '', 'search' => '', 'per_page' => 20, 'page' => 1, 'orderby' => 'created_at', 'order' => 'DESC' ];
        $args     = wp_parse_args( $args, $defaults );
        $where    = [ '1=1' ];
        $values   = [];

        if ( $args['status'] ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }
        if ( $args['search'] ) {
            $like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where[]  = '(name LIKE %s OR email LIKE %s OR subject LIKE %s)';
            $values[] = $like; $values[] = $like; $values[] = $like;
        }

        $where_sql = implode( ' AND ', $where );
        $order_sql = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] ) ?: 'created_at DESC';
        $offset    = ( absint( $args['page'] ) - 1 ) * absint( $args['per_page'] );
        $query     = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$order_sql} LIMIT %d OFFSET %d";
        $values[]  = absint( $args['per_page'] );
        $values[]  = $offset;

        return $wpdb->get_results( $wpdb->prepare( $query, $values ) );
    }

    public static function insert_contact( $data ) {
        global $wpdb;
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $data['created_at'] = current_time( 'mysql' );
        $data['updated_at'] = current_time( 'mysql' );
        return $wpdb->insert( $wpdb->prefix . 'nth_contacts', $data );
    }

    public static function mark_contact_read( $id ) {
        global $wpdb;
        return $wpdb->update(
            $wpdb->prefix . 'nth_contacts',
            [ 'status' => 'read', 'updated_at' => current_time( 'mysql' ) ],
            [ 'id' => absint( $id ) ]
        );
    }

    public static function delete_contact( $id ) {
        global $wpdb;
        return $wpdb->delete( $wpdb->prefix . 'nth_contacts', [ 'id' => absint( $id ) ] );
    }

    // ── JOB APPLICATIONS ──────────────────────────────────────────────────────

    public static function get_applications( $args = [] ) {
        global $wpdb;
        $table    = $wpdb->prefix . 'nth_applications';
        $defaults = [ 'status' => '', 'job_id' => 0, 'search' => '', 'per_page' => 20, 'page' => 1, 'orderby' => 'created_at', 'order' => 'DESC' ];
        $args     = wp_parse_args( $args, $defaults );
        $where    = [ '1=1' ];
        $values   = [];

        if ( $args['status'] ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }
        if ( $args['job_id'] ) {
            $where[]  = 'job_id = %d';
            $values[] = absint( $args['job_id'] );
        }
        if ( $args['search'] ) {
            $like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where[]  = '(applicant_name LIKE %s OR applicant_email LIKE %s OR job_title LIKE %s)';
            $values[] = $like; $values[] = $like; $values[] = $like;
        }

        $where_sql = implode( ' AND ', $where );
        $order_sql = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] ) ?: 'created_at DESC';
        $offset    = ( absint( $args['page'] ) - 1 ) * absint( $args['per_page'] );
        $query     = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$order_sql} LIMIT %d OFFSET %d";
        $values[]  = absint( $args['per_page'] );
        $values[]  = $offset;

        return $wpdb->get_results( $wpdb->prepare( $query, $values ) );
    }

    public static function insert_application( $data ) {
        global $wpdb;
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $data['created_at'] = current_time( 'mysql' );
        $data['updated_at'] = current_time( 'mysql' );
        return $wpdb->insert( $wpdb->prefix . 'nth_applications', $data );
    }

    public static function update_application_status( $id, $status, $notes = '' ) {
        global $wpdb;
        return $wpdb->update(
            $wpdb->prefix . 'nth_applications',
            [ 'status' => sanitize_text_field( $status ), 'admin_notes' => sanitize_textarea_field( $notes ), 'updated_at' => current_time( 'mysql' ) ],
            [ 'id' => absint( $id ) ]
        );
    }

    public static function delete_application( $id ) {
        global $wpdb;
        return $wpdb->delete( $wpdb->prefix . 'nth_applications', [ 'id' => absint( $id ) ] );
    }

    // ── ANALYTICS ─────────────────────────────────────────────────────────────

    public static function get_summary_stats() {
        global $wpdb;
        $today      = current_time( 'Y-m-d' );
        $month_start = date( 'Y-m-01', strtotime( $today ) );

        return [
            'reservations_total'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_reservations" ),
            'reservations_pending' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_reservations WHERE status='pending'" ),
            'reservations_today'   => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_reservations WHERE DATE(created_at)=%s", $today ) ),
            'reservations_month'   => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_reservations WHERE created_at >= %s", $month_start ) ),
            'contacts_total'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_contacts" ),
            'contacts_unread'      => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_contacts WHERE status='unread'" ),
            'applications_total'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_applications" ),
            'applications_pending' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nth_applications WHERE status='pending'" ),
            'guests_this_month'    => (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(number_of_guests) FROM {$wpdb->prefix}nth_reservations WHERE created_at >= %s AND status != 'cancelled'", $month_start ) ),
        ];
    }

    public static function get_reservations_by_day( $days = 30 ) {
        global $wpdb;
        $from = date( 'Y-m-d', strtotime( "-{$days} days" ) );
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT DATE(reservation_date) as date, COUNT(*) as count, SUM(number_of_guests) as guests
             FROM {$wpdb->prefix}nth_reservations
             WHERE reservation_date >= %s AND status != 'cancelled'
             GROUP BY DATE(reservation_date)
             ORDER BY date ASC",
            $from
        ) );
    }
}
