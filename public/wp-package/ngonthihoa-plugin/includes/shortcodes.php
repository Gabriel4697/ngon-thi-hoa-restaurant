<?php
/**
 * Shortcodes for Ngon Thi Hoa
 * [nth_reservation_form], [nth_contact_form], [nth_menu_grid], [nth_language_switcher]
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Shortcodes {

    public function __construct() {
        add_shortcode( 'nth_reservation_form', [ $this, 'reservation_form' ] );
        add_shortcode( 'nth_contact_form',     [ $this, 'contact_form' ] );
        add_shortcode( 'nth_menu_grid',        [ $this, 'menu_grid' ] );
        add_shortcode( 'nth_language_switcher', [ $this, 'language_switcher' ] );
        add_shortcode( 'nth_opening_hours',    [ $this, 'opening_hours' ] );
        add_shortcode( 'nth_google_map',       [ $this, 'google_map' ] );
    }

    public function reservation_form( $atts ) {
        $atts = shortcode_atts( [ 'class' => '' ], $atts );
        ob_start();
        ?>
        <div class="nth-form-wrap nth-reservation-form <?php echo esc_attr( $atts['class'] ); ?>">
            <form id="nth-reservation-form" class="nth-form" novalidate>
                <?php wp_nonce_field( 'nth_nonce', 'nth_reservation_nonce' ); ?>
                <div class="nth-form-grid">
                    <div class="nth-form-field">
                        <label for="nth-res-name"><?php _e( 'Họ và tên *', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="text" id="nth-res-name" name="name" required maxlength="100" />
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-res-phone"><?php _e( 'Số điện thoại *', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="tel" id="nth-res-phone" name="phone" required maxlength="30" />
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-res-email"><?php _e( 'Email *', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="email" id="nth-res-email" name="email" required maxlength="100" />
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-res-guests"><?php _e( 'Số khách *', NTH_TEXT_DOMAIN ); ?></label>
                        <select id="nth-res-guests" name="number_of_guests" required>
                            <?php for ( $i = 1; $i <= 20; $i++ ) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-res-date"><?php _e( 'Ngày đặt *', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="date" id="nth-res-date" name="reservation_date" required
                               min="<?php echo date( 'Y-m-d' ); ?>" />
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-res-time"><?php _e( 'Giờ đặt *', NTH_TEXT_DOMAIN ); ?></label>
                        <select id="nth-res-time" name="reservation_time" required>
                            <?php
                            $times = ['06:30','07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30',
                                      '12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30',
                                      '17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30'];
                            foreach ( $times as $t ) :
                            ?>
                            <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="nth-form-field nth-form-field-full">
                        <label for="nth-res-notes"><?php _e( 'Ghi chú (tùy chọn)', NTH_TEXT_DOMAIN ); ?></label>
                        <textarea id="nth-res-notes" name="notes" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <?php do_action( 'nth_before_form_close' ); ?>
                <div class="nth-form-submit">
                    <button type="submit" class="nth-btn nth-btn-primary">
                        <?php _e( 'Đặt Bàn Ngay', NTH_TEXT_DOMAIN ); ?>
                    </button>
                </div>
                <div class="nth-form-message" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function contact_form( $atts ) {
        $atts = shortcode_atts( [ 'class' => '' ], $atts );
        ob_start();
        ?>
        <div class="nth-form-wrap nth-contact-form <?php echo esc_attr( $atts['class'] ); ?>">
            <form id="nth-contact-form" class="nth-form" novalidate>
                <?php wp_nonce_field( 'nth_nonce', 'nth_contact_nonce' ); ?>
                <div class="nth-form-grid">
                    <div class="nth-form-field">
                        <label for="nth-con-name"><?php _e( 'Họ và tên *', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="text" id="nth-con-name" name="name" required maxlength="100" />
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-con-email"><?php _e( 'Email *', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="email" id="nth-con-email" name="email" required maxlength="100" />
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-con-phone"><?php _e( 'Điện thoại', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="tel" id="nth-con-phone" name="phone" maxlength="30" />
                    </div>
                    <div class="nth-form-field">
                        <label for="nth-con-subject"><?php _e( 'Chủ đề', NTH_TEXT_DOMAIN ); ?></label>
                        <input type="text" id="nth-con-subject" name="subject" maxlength="200" />
                    </div>
                    <div class="nth-form-field nth-form-field-full">
                        <label for="nth-con-message"><?php _e( 'Nội dung *', NTH_TEXT_DOMAIN ); ?></label>
                        <textarea id="nth-con-message" name="message" rows="5" required maxlength="2000"></textarea>
                    </div>
                </div>
                <?php do_action( 'nth_before_form_close' ); ?>
                <div class="nth-form-submit">
                    <button type="submit" class="nth-btn nth-btn-primary">
                        <?php _e( 'Gửi Liên Hệ', NTH_TEXT_DOMAIN ); ?>
                    </button>
                </div>
                <div class="nth-form-message" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function menu_grid( $atts ) {
        $atts = shortcode_atts( [
            'category' => '',
            'group'    => '',
            'count'    => 12,
            'columns'  => 3,
        ], $atts );

        $query_args = [
            'post_type'      => 'menu_item',
            'posts_per_page' => absint( $atts['count'] ),
            'post_status'    => 'publish',
        ];

        if ( $atts['category'] ) {
            $query_args['tax_query'] = [[
                'taxonomy' => 'menu_category',
                'field'    => 'slug',
                'terms'    => explode( ',', $atts['category'] ),
            ]];
        }
        if ( $atts['group'] ) {
            $query_args['meta_query'] = [[
                'key'   => '_nth_menu_group',
                'value' => $atts['group'],
            ]];
        }

        $query = new WP_Query( $query_args );
        ob_start();
        if ( $query->have_posts() ) :
        ?>
        <div class="nth-menu-grid nth-cols-<?php echo absint( $atts['columns'] ); ?>">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <div class="nth-menu-item">
                <?php if ( has_post_thumbnail() ) : ?>
                <div class="nth-menu-item-img">
                    <?php the_post_thumbnail( 'medium' ); ?>
                </div>
                <?php endif; ?>
                <div class="nth-menu-item-info">
                    <h3 class="nth-menu-item-name"><?php the_title(); ?></h3>
                    <?php
                    $price = get_post_meta( get_the_ID(), '_nth_price', true );
                    $unit  = get_post_meta( get_the_ID(), '_nth_price_unit', true );
                    if ( $price ) :
                    ?>
                    <div class="nth-menu-item-price"><?php echo esc_html( number_format( $price ) . 'đ' . ( $unit ? ' / ' . $unit : '' ) ); ?></div>
                    <?php endif; ?>
                    <?php
                    $desc = get_post_meta( get_the_ID(), '_nth_description', true );
                    if ( $desc ) :
                    ?>
                    <p class="nth-menu-item-desc"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php
        else :
            echo '<p>' . __( 'Không có món ăn nào.', NTH_TEXT_DOMAIN ) . '</p>';
        endif;
        return ob_get_clean();
    }

    public function language_switcher( $atts ) {
        $enabled = (array) get_option( 'nth_languages_enabled', ['vi','en','zh','ko'] );
        $current = NTH_Multilingual::get_current_language();
        ob_start();
        ?>
        <div class="nth-language-switcher">
            <?php foreach ( NTH_Multilingual::$languages as $code => $info ) : ?>
            <?php if ( ! in_array( $code, $enabled, true ) ) continue; ?>
            <button class="nth-lang-btn <?php echo $code === $current ? 'nth-lang-active' : ''; ?>"
                    data-nth-lang="<?php echo esc_attr( $code ); ?>"
                    title="<?php echo esc_attr( $info['name'] ); ?>">
                <?php echo esc_html( $info['flag'] ); ?>
                <span><?php echo esc_html( $info['name'] ); ?></span>
            </button>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function opening_hours( $atts ) {
        $hours = get_option( 'nth_restaurant_hours', 'Hằng ngày / Daily: 6:30 - 22:00' );
        return '<span class="nth-opening-hours">' . esc_html( $hours ) . '</span>';
    }

    public function google_map( $atts ) {
        $atts   = shortcode_atts( [ 'height' => '450' ], $atts );
        $embed  = get_option( 'nth_google_maps_embed' );
        if ( ! $embed ) {
            return '<p>' . __( 'Vui lòng cấu hình Google Maps embed URL trong Cài đặt nhà hàng.', NTH_TEXT_DOMAIN ) . '</p>';
        }
        return '<div class="nth-map-wrap"><iframe src="' . esc_url( $embed ) . '" width="100%" height="' . absint( $atts['height'] ) . '" style="border:0;" allowfullscreen loading="lazy"></iframe></div>';
    }
}

new NTH_Shortcodes();
