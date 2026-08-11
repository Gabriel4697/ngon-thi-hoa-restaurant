<?php
/**
 * Template Name: Home Page
 * Front page template for Ngon Thi Hoa
 */
get_header();
?>

<main id="main-content">

<!-- ═══════════════════════ HERO SLIDER ═══════════════════════ -->
<section class="hero-slider" aria-label="Hero">
    <?php
    $slides = ngonthihoa_get_hero_slides();
    foreach ( $slides as $i => $slide ) :
        $active = $i === 0 ? ' active' : '';
    ?>
    <div class="hero-slide<?php echo $active; ?>">
        <img src="<?php echo esc_url( $slide['image'] ); ?>" alt="<?php echo esc_attr( $slide['title'] ); ?>" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>">
        <div class="hero-content">
            <h1><?php echo esc_html( $slide['title'] ); ?></h1>
            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                <p class="subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
            <?php endif; ?>
            <button class="btn-reserve" onclick="document.getElementById('reservation-modal').classList.add('open')">
                <?php esc_html_e( 'Đặt Bàn Ngay', 'ngonthihoa' ); ?>
            </button>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="hero-dots">
        <?php foreach ( $slides as $i => $_ ) : ?>
            <button class="hero-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></button>
        <?php endforeach; ?>
    </div>
</section>

<!-- ═══════════════════════ ABOUT ═══════════════════════ -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-img">
                <?php
                $about_img_id = get_theme_mod('ngonthihoa_about_image');
                if ( $about_img_id ) {
                    echo wp_get_attachment_image( $about_img_id, 'ngonthihoa-card', false, ['alt'=>'About Ngon Thi Hoa'] );
                } else {
                    echo '<div style="background:linear-gradient(135deg,#5e4743,#3d2e2b);width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ffc952;font-family:\'Dancing Script\',cursive;font-size:32px;">Ngon Thị Hoa</div>';
                }
                ?>
            </div>
            <div>
                <span class="section-label"><?php esc_html_e( 'Về Chúng Tôi', 'ngonthihoa' ); ?></span>
                <h2 class="section-title"><?php esc_html_e( 'Ngon Thị Hoa - Tropical Garden', 'ngonthihoa' ); ?></h2>
                <div class="brush-divider left"></div>
                <p style="color:#666;line-height:1.8;margin-bottom:16px;">
                    <?php esc_html_e( 'Nhà hàng Ngon Thị Hoa được thành lập với mong muốn mang đến trải nghiệm ẩm thực Việt Nam đích thực trong không gian xanh mát, thân thiện và ấm cúng tại Đà Nẵng.', 'ngonthihoa' ); ?>
                </p>
                <p style="color:#666;line-height:1.8;margin-bottom:24px;">
                    <?php esc_html_e( 'Với đội ngũ đầu bếp tay nghề cao và nguyên liệu tươi sạch được chọn lọc kỹ càng, mỗi món ăn tại Ngon Thị Hoa là một tác phẩm ẩm thực đậm đà hương vị truyền thống.', 'ngonthihoa' ); ?>
                </p>
                <div class="about-features">
                    <?php
                    $features = [
                        ['🌿', 'Nguyên liệu tươi sạch', 'Fresh ingredients'],
                        ['👨‍🍳', 'Đầu bếp tay nghề cao', 'Skilled chefs'],
                        ['🏡', 'Không gian xanh mát', 'Garden ambiance'],
                        ['⭐', 'Phục vụ chuyên nghiệp', 'Professional service'],
                    ];
                    foreach ( $features as [$icon, $title, $sub] ) {
                        echo '<div class="feature-item">
                            <div class="feature-icon">' . $icon . '</div>
                            <div><strong style="display:block;color:#5e4743;font-size:14px;">' . esc_html($title) . '</strong><span style="font-size:13px;color:#888;">' . esc_html($sub) . '</span></div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════ MENU PREVIEW ═══════════════════════ -->
<section class="menu-section">
    <div class="container">
        <div class="text-center" style="margin-bottom:48px;">
            <span class="section-label"><?php esc_html_e( 'Thực Đơn', 'ngonthihoa' ); ?></span>
            <h2 class="section-title"><?php esc_html_e( 'Khám Phá Thực Đơn', 'ngonthihoa' ); ?></h2>
            <div class="brush-divider"></div>
        </div>

        <!-- Tabs -->
        <div class="menu-tabs" id="menu-group-tabs">
            <?php
            $groups = [
                'sang'           => 'Sáng',
                'trua_toi'       => 'Trưa & Tối',
                'do_uong'        => 'Đồ Uống',
                'do_uong_co_con' => 'Đồ Uống Có Cồn',
                'ruou_vang'      => 'Rượu Vang',
            ];
            $active_group = sanitize_key( $_GET['group'] ?? 'sang' );
            foreach ( $groups as $slug => $label ) {
                $active = $slug === $active_group ? ' active' : '';
                echo '<button class="menu-tab' . $active . '" data-group="' . esc_attr($slug) . '">' . esc_html($label) . '</button>';
            }
            ?>
        </div>

        <!-- Menu Items Grid -->
        <div class="menu-items-grid" id="menu-items-grid">
            <?php
            $args = [
                'post_type'      => 'menu_item',
                'posts_per_page' => 8,
                'tax_query'      => [[
                    'taxonomy' => 'menu_group',
                    'field'    => 'slug',
                    'terms'    => $active_group,
                ]],
            ];
            $menu_query = new WP_Query( $args );
            if ( $menu_query->have_posts() ) :
                while ( $menu_query->have_posts() ) : $menu_query->the_post();
                    $price = get_post_meta( get_the_ID(), '_menu_price', true );
                    $desc  = get_post_meta( get_the_ID(), '_menu_description_en', true ) ?: get_the_excerpt();
                    ?>
                    <div class="menu-item-card">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="menu-item-img"><?php the_post_thumbnail('ngonthihoa-thumb'); ?></div>
                        <?php endif; ?>
                        <div class="menu-item-body">
                            <h3 class="menu-item-name"><?php the_title(); ?></h3>
                            <?php if ( $desc ) : ?>
                                <p class="menu-item-desc"><?php echo esc_html($desc); ?></p>
                            <?php endif; ?>
                            <?php if ( $price ) : ?>
                                <p class="menu-item-price"><?php echo esc_html($price); ?> đ</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p style="grid-column:1/-1;text-align:center;color:#999;padding:40px 0;">' . esc_html__('Đang cập nhật thực đơn...','ngonthihoa') . '</p>';
            endif;
            ?>
        </div>

        <div class="text-center mt-8">
            <a href="<?php echo esc_url( home_url('/menu/') ); ?>" class="btn-reserve">
                <?php esc_html_e( 'Xem Toàn Bộ Thực Đơn', 'ngonthihoa' ); ?>
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════ BLOG / NEWS ═══════════════════════ -->
<section class="blog-section vn-pattern-bg">
    <div class="container">
        <div class="text-center" style="margin-bottom:48px;">
            <span class="section-label"><?php esc_html_e( 'Tin Tức', 'ngonthihoa' ); ?></span>
            <h2 class="section-title"><?php esc_html_e( 'Tin Tức Mới Nhất', 'ngonthihoa' ); ?></h2>
            <div class="brush-divider"></div>
        </div>

        <div class="blog-grid">
            <?php
            $news_query = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'post_status'    => 'publish',
            ]);
            if ( $news_query->have_posts() ) :
                while ( $news_query->have_posts() ) : $news_query->the_post();
                    ?>
                    <article class="blog-card">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="blog-card-img">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('ngonthihoa-blog'); ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="blog-card-body">
                            <p class="blog-card-date"><?php echo get_the_date('d/m/Y'); ?></p>
                            <h3 class="blog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="blog-card-excerpt"><?php the_excerpt(); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn-read-more"><?php esc_html_e( 'Xem thêm →', 'ngonthihoa' ); ?></a>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <div class="text-center mt-8">
            <a href="<?php echo esc_url( home_url('/blog/') ); ?>" class="btn-reserve">
                <?php esc_html_e( 'Xem Tất Cả Bài Viết', 'ngonthihoa' ); ?>
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════ MEDIA TEASER ═══════════════════════ -->
<section class="media-section">
    <div class="container">
        <div class="text-center" style="margin-bottom:48px;">
            <span class="section-label">Media</span>
            <h2 class="section-title"><?php esc_html_e( 'Hình Ảnh & Video', 'ngonthihoa' ); ?></h2>
            <div class="brush-divider"></div>
        </div>

        <div class="media-grid">
            <?php
            $media_args = [
                'post_type'      => 'media_item',
                'posts_per_page' => 6,
                'post_status'    => 'publish',
            ];
            $media_query = new WP_Query( $media_args );
            if ( $media_query->have_posts() ) :
                while ( $media_query->have_posts() ) : $media_query->the_post();
                    $media_type = get_post_meta( get_the_ID(), '_media_type', true ) ?: 'image';
                    ?>
                    <div class="media-item">
                        <?php if ( $media_type === 'video' ) : ?>
                            <?php $video_url = get_post_meta( get_the_ID(), '_media_video_url', true ); ?>
                            <video src="<?php echo esc_url($video_url); ?>" muted loop></video>
                        <?php elseif ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('ngonthihoa-square'); ?>
                        <?php endif; ?>
                        <div class="media-item-overlay">
                            <?php echo $media_type === 'video' ? '▶' : '⊕'; ?>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <div class="text-center mt-8">
            <a href="<?php echo esc_url( home_url('/media/') ); ?>" class="btn-reserve">
                <?php esc_html_e( 'Xem Thêm', 'ngonthihoa' ); ?>
            </a>
        </div>
    </div>
</section>

</main>

<?php get_footer(); ?>
