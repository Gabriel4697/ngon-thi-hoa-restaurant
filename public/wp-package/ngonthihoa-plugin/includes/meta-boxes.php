<?php
/**
 * Meta Boxes for Ngon Thi Hoa
 * Custom fields for menu items, media, jobs
 * Multilingual fields (VI/EN/ZH/KO) with WPML/Polylang compatibility
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Meta_Boxes {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'register' ] );
        add_action( 'save_post', [ $this, 'save' ], 10, 2 );
    }

    public function register() {
        // Menu item fields
        add_meta_box( 'nth_menu_details', __( 'Chi tiết món ăn', NTH_TEXT_DOMAIN ), [ $this, 'render_menu_meta' ], 'menu_item', 'normal', 'high' );
        add_meta_box( 'nth_menu_translations', __( 'Tên & mô tả đa ngôn ngữ', NTH_TEXT_DOMAIN ), [ $this, 'render_translations_meta' ], 'menu_item', 'normal', 'default' );

        // Media fields
        add_meta_box( 'nth_media_details', __( 'Chi tiết media', NTH_TEXT_DOMAIN ), [ $this, 'render_media_meta' ], 'media_gallery', 'normal', 'high' );

        // Job position fields
        add_meta_box( 'nth_job_details', __( 'Chi tiết tuyển dụng', NTH_TEXT_DOMAIN ), [ $this, 'render_job_meta' ], 'job_position', 'normal', 'high' );

        // Blog post extra fields
        add_meta_box( 'nth_blog_details', __( 'Thông tin bổ sung', NTH_TEXT_DOMAIN ), [ $this, 'render_blog_meta' ], 'blog_post', 'side', 'default' );
    }

    public function render_menu_meta( $post ) {
        wp_nonce_field( 'nth_save_meta', 'nth_meta_nonce' );
        $price      = get_post_meta( $post->ID, '_nth_price', true );
        $price_unit = get_post_meta( $post->ID, '_nth_price_unit', true );
        $price_alt  = get_post_meta( $post->ID, '_nth_price_alt', true );
        $price_alt_label = get_post_meta( $post->ID, '_nth_price_alt_label', true );
        $group      = get_post_meta( $post->ID, '_nth_menu_group', true );
        $featured   = get_post_meta( $post->ID, '_nth_featured', true );
        $available  = get_post_meta( $post->ID, '_nth_available', true );
        $sort_order = get_post_meta( $post->ID, '_nth_sort_order', true );

        $groups = [ '' => __('-- Chọn nhóm --',NTH_TEXT_DOMAIN), 'sang'=>'Sáng', 'trua_toi'=>'Trưa và Tối', 'do_uong'=>'Đồ Uống', 'do_uong_co_con'=>'Đồ Uống Có Cồn', 'ruou_vang'=>'Rượu Vang' ];
        ?>
        <table class="form-table nth-meta-table">
            <tr>
                <th><label><?php _e('Nhóm thực đơn',NTH_TEXT_DOMAIN); ?></label></th>
                <td>
                    <select name="_nth_menu_group">
                        <?php foreach ( $groups as $k => $v ) : ?>
                        <option value="<?php echo esc_attr($k); ?>" <?php selected($group,$k); ?>><?php echo esc_html($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Giá (VND)',NTH_TEXT_DOMAIN); ?></label></th>
                <td>
                    <input type="number" name="_nth_price" value="<?php echo esc_attr($price); ?>" min="0" step="1000" class="regular-text" />
                    <select name="_nth_price_unit" style="margin-left:8px;">
                        <option value="" <?php selected($price_unit,''); ?>><?php _e('-- Đơn vị --',NTH_TEXT_DOMAIN); ?></option>
                        <option value="phần" <?php selected($price_unit,'phần'); ?>><?php _e('phần',NTH_TEXT_DOMAIN); ?></option>
                        <option value="ly" <?php selected($price_unit,'ly'); ?>><?php _e('ly',NTH_TEXT_DOMAIN); ?></option>
                        <option value="chai" <?php selected($price_unit,'chai'); ?>><?php _e('chai',NTH_TEXT_DOMAIN); ?></option>
                        <option value="kg" <?php selected($price_unit,'kg'); ?>><?php _e('kg',NTH_TEXT_DOMAIN); ?></option>
                        <option value="100g" <?php selected($price_unit,'100g'); ?>>100g</option>
                        <option value="shot" <?php selected($price_unit,'shot'); ?>>shot</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Giá thứ hai (tùy chọn)',NTH_TEXT_DOMAIN); ?></label></th>
                <td>
                    <input type="number" name="_nth_price_alt" value="<?php echo esc_attr($price_alt); ?>" min="0" step="1000" class="small-text" />
                    <input type="text" name="_nth_price_alt_label" value="<?php echo esc_attr($price_alt_label); ?>" placeholder="VD: chai, phần lớn..." style="width:140px;margin-left:8px;" />
                    <p class="description"><?php _e('Dùng khi có 2 mức giá (VD: shot/chai, nhỏ/lớn)',NTH_TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Thứ tự hiển thị',NTH_TEXT_DOMAIN); ?></label></th>
                <td><input type="number" name="_nth_sort_order" value="<?php echo esc_attr($sort_order ?: 0); ?>" class="small-text" /></td>
            </tr>
            <tr>
                <th><?php _e('Tùy chọn',NTH_TEXT_DOMAIN); ?></th>
                <td>
                    <label><input type="checkbox" name="_nth_featured" value="1" <?php checked($featured,1); ?> /> <?php _e('Món đặc sắc / nổi bật',NTH_TEXT_DOMAIN); ?></label><br>
                    <label><input type="checkbox" name="_nth_available" value="1" <?php checked($available !== '0',true); ?> /> <?php _e('Đang có sẵn',NTH_TEXT_DOMAIN); ?></label>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_translations_meta( $post ) {
        $name_en  = get_post_meta( $post->ID, '_nth_name_en', true );
        $name_zh  = get_post_meta( $post->ID, '_nth_name_zh', true );
        $name_ko  = get_post_meta( $post->ID, '_nth_name_ko', true );
        $desc_vi  = get_post_meta( $post->ID, '_nth_description', true );
        $desc_en  = get_post_meta( $post->ID, '_nth_description_en', true );
        $desc_zh  = get_post_meta( $post->ID, '_nth_description_zh', true );
        $desc_ko  = get_post_meta( $post->ID, '_nth_description_ko', true );
        ?>
        <p class="description"><?php _e('Điền tên và mô tả bằng các ngôn ngữ khác để hiển thị đúng khi khách chọn ngôn ngữ. Để trống nếu dùng tên Tiếng Việt cho tất cả.',NTH_TEXT_DOMAIN); ?></p>
        <table class="form-table nth-meta-table">
            <tr>
                <th><?php _e('Mô tả (VI)',NTH_TEXT_DOMAIN); ?></th>
                <td><textarea name="_nth_description" rows="2" class="large-text"><?php echo esc_textarea($desc_vi); ?></textarea></td>
            </tr>
            <tr>
                <th><?php _e('Tên (EN)',NTH_TEXT_DOMAIN); ?></th>
                <td>
                    <input type="text" name="_nth_name_en" value="<?php echo esc_attr($name_en); ?>" class="regular-text" />
                    <br>
                    <textarea name="_nth_description_en" rows="2" class="large-text" placeholder="Description (EN)"><?php echo esc_textarea($desc_en); ?></textarea>
                </td>
            </tr>
            <tr>
                <th><?php _e('Tên (ZH 中文)',NTH_TEXT_DOMAIN); ?></th>
                <td>
                    <input type="text" name="_nth_name_zh" value="<?php echo esc_attr($name_zh); ?>" class="regular-text" />
                    <br>
                    <textarea name="_nth_description_zh" rows="2" class="large-text" placeholder="描述 (ZH)"><?php echo esc_textarea($desc_zh); ?></textarea>
                </td>
            </tr>
            <tr>
                <th><?php _e('Tên (KO 한국어)',NTH_TEXT_DOMAIN); ?></th>
                <td>
                    <input type="text" name="_nth_name_ko" value="<?php echo esc_attr($name_ko); ?>" class="regular-text" />
                    <br>
                    <textarea name="_nth_description_ko" rows="2" class="large-text" placeholder="설명 (KO)"><?php echo esc_textarea($desc_ko); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_media_meta( $post ) {
        $type     = get_post_meta( $post->ID, '_nth_media_type', true );
        $video    = get_post_meta( $post->ID, '_nth_video_url', true );
        $alt_img  = get_post_meta( $post->ID, '_nth_alt_image', true );
        $featured = get_post_meta( $post->ID, '_nth_featured', true );
        ?>
        <table class="form-table nth-meta-table">
            <tr>
                <th><label><?php _e('Loại media',NTH_TEXT_DOMAIN); ?></label></th>
                <td>
                    <label><input type="radio" name="_nth_media_type" value="image" <?php checked($type !== 'video',true); ?> /> <?php _e('Hình ảnh',NTH_TEXT_DOMAIN); ?></label>
                    <label style="margin-left:16px;"><input type="radio" name="_nth_media_type" value="video" <?php checked($type,'video'); ?> /> <?php _e('Video',NTH_TEXT_DOMAIN); ?></label>
                </td>
            </tr>
            <tr class="nth-video-row" style="<?php echo $type !== 'video' ? 'display:none;' : ''; ?>">
                <th><label><?php _e('URL Video (YouTube/Vimeo)',NTH_TEXT_DOMAIN); ?></label></th>
                <td>
                    <input type="url" name="_nth_video_url" value="<?php echo esc_url($video); ?>" class="large-text" placeholder="https://www.youtube.com/embed/..." />
                    <p class="description"><?php _e('Dùng đường dẫn embed của YouTube hoặc Vimeo',NTH_TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Ảnh phụ / ảnh thumbnail cho video',NTH_TEXT_DOMAIN); ?></label></th>
                <td>
                    <input type="hidden" name="_nth_alt_image" id="nth_alt_image" value="<?php echo esc_url($alt_img); ?>" />
                    <?php if ($alt_img) : ?><img src="<?php echo esc_url($alt_img); ?>" style="max-width:200px;display:block;margin-bottom:8px;" /><?php endif; ?>
                    <button type="button" class="button nth-media-upload" data-target="nth_alt_image"><?php _e('Chọn ảnh',NTH_TEXT_DOMAIN); ?></button>
                </td>
            </tr>
            <tr>
                <th><?php _e('Tùy chọn',NTH_TEXT_DOMAIN); ?></th>
                <td><label><input type="checkbox" name="_nth_featured" value="1" <?php checked($featured,1); ?> /> <?php _e('Hiển thị nổi bật',NTH_TEXT_DOMAIN); ?></label></td>
            </tr>
        </table>
        <script>
        jQuery(document).ready(function($) {
            $('[name="_nth_media_type"]').change(function() {
                if ($(this).val() === 'video') $('.nth-video-row').show();
                else $('.nth-video-row').hide();
            });
            $('.nth-media-upload').click(function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                var frame = wp.media({ title: '<?php _e('Chọn ảnh',NTH_TEXT_DOMAIN); ?>', multiple: false });
                frame.on('select', function() {
                    var att = frame.state().get('selection').first().toJSON();
                    $('#' + target).val(att.url);
                    $('#' + target).prev('img').remove();
                    $('#' + target).before('<img src="' + att.url + '" style="max-width:200px;display:block;margin-bottom:8px;" />');
                });
                frame.open();
            });
        });
        </script>
        <?php
    }

    public function render_job_meta( $post ) {
        $salary   = get_post_meta( $post->ID, '_nth_salary', true );
        $type     = get_post_meta( $post->ID, '_nth_job_type', true );
        $location = get_post_meta( $post->ID, '_nth_job_location', true );
        $deadline = get_post_meta( $post->ID, '_nth_deadline', true );
        $openings = get_post_meta( $post->ID, '_nth_openings', true );
        $requirements = get_post_meta( $post->ID, '_nth_requirements', true );
        $benefits     = get_post_meta( $post->ID, '_nth_benefits', true );
        ?>
        <table class="form-table nth-meta-table">
            <tr>
                <th><label><?php _e('Mức lương',NTH_TEXT_DOMAIN); ?></label></th>
                <td><input type="text" name="_nth_salary" value="<?php echo esc_attr($salary); ?>" class="regular-text" placeholder="VD: 7,000,000 - 12,000,000 VND" /></td>
            </tr>
            <tr>
                <th><label><?php _e('Loại hình',NTH_TEXT_DOMAIN); ?></label></th>
                <td>
                    <select name="_nth_job_type">
                        <option value="full_time" <?php selected($type,'full_time'); ?>><?php _e('Toàn thời gian',NTH_TEXT_DOMAIN); ?></option>
                        <option value="part_time" <?php selected($type,'part_time'); ?>><?php _e('Bán thời gian',NTH_TEXT_DOMAIN); ?></option>
                        <option value="intern" <?php selected($type,'intern'); ?>><?php _e('Thực tập',NTH_TEXT_DOMAIN); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Địa điểm',NTH_TEXT_DOMAIN); ?></label></th>
                <td><input type="text" name="_nth_job_location" value="<?php echo esc_attr($location ?: 'Đà Nẵng'); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label><?php _e('Hạn nộp hồ sơ',NTH_TEXT_DOMAIN); ?></label></th>
                <td><input type="date" name="_nth_deadline" value="<?php echo esc_attr($deadline); ?>" /></td>
            </tr>
            <tr>
                <th><label><?php _e('Số lượng tuyển',NTH_TEXT_DOMAIN); ?></label></th>
                <td><input type="number" name="_nth_openings" value="<?php echo esc_attr($openings ?: 1); ?>" min="1" class="small-text" /></td>
            </tr>
            <tr>
                <th><label><?php _e('Yêu cầu',NTH_TEXT_DOMAIN); ?></label></th>
                <td><textarea name="_nth_requirements" rows="5" class="large-text"><?php echo esc_textarea($requirements); ?></textarea></td>
            </tr>
            <tr>
                <th><label><?php _e('Quyền lợi',NTH_TEXT_DOMAIN); ?></label></th>
                <td><textarea name="_nth_benefits" rows="5" class="large-text"><?php echo esc_textarea($benefits); ?></textarea></td>
            </tr>
        </table>
        <?php
    }

    public function render_blog_meta( $post ) {
        $subtitle  = get_post_meta( $post->ID, '_nth_subtitle', true );
        $read_time = get_post_meta( $post->ID, '_nth_read_time', true );
        $featured  = get_post_meta( $post->ID, '_nth_featured', true );
        ?>
        <table class="form-table nth-meta-table">
            <tr>
                <th><?php _e('Tiêu đề phụ',NTH_TEXT_DOMAIN); ?></th>
                <td><input type="text" name="_nth_subtitle" value="<?php echo esc_attr($subtitle); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><?php _e('Thời gian đọc (phút)',NTH_TEXT_DOMAIN); ?></th>
                <td><input type="number" name="_nth_read_time" value="<?php echo esc_attr($read_time ?: 5); ?>" min="1" class="small-text" /></td>
            </tr>
            <tr>
                <th><?php _e('Bài viết nổi bật',NTH_TEXT_DOMAIN); ?></th>
                <td><label><input type="checkbox" name="_nth_featured" value="1" <?php checked($featured,1); ?> /> <?php _e('Hiển thị ở trang chủ',NTH_TEXT_DOMAIN); ?></label></td>
            </tr>
        </table>
        <?php
    }

    public function save( $post_id, $post ) {
        if ( ! isset( $_POST['nth_meta_nonce'] ) || ! wp_verify_nonce( $_POST['nth_meta_nonce'], 'nth_save_meta' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $text_fields = [
            '_nth_menu_group', '_nth_price_unit', '_nth_price_alt_label',
            '_nth_name_en', '_nth_name_zh', '_nth_name_ko',
            '_nth_description', '_nth_description_en', '_nth_description_zh', '_nth_description_ko',
            '_nth_media_type', '_nth_salary', '_nth_job_type', '_nth_job_location',
            '_nth_deadline', '_nth_subtitle',
        ];
        $int_fields  = [ '_nth_price', '_nth_price_alt', '_nth_sort_order', '_nth_openings', '_nth_read_time' ];
        $url_fields  = [ '_nth_video_url', '_nth_alt_image' ];
        $textarea_fields = [ '_nth_requirements', '_nth_benefits' ];
        $bool_fields = [ '_nth_featured', '_nth_available' ];

        foreach ( $text_fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
        foreach ( $int_fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, $field, absint( $_POST[ $field ] ) );
            }
        }
        foreach ( $url_fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, $field, esc_url_raw( $_POST[ $field ] ) );
            }
        }
        foreach ( $textarea_fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_textarea_field( $_POST[ $field ] ) );
            }
        }
        foreach ( $bool_fields as $field ) {
            update_post_meta( $post_id, $field, isset( $_POST[ $field ] ) ? 1 : 0 );
        }
    }
}

new NTH_Meta_Boxes();
