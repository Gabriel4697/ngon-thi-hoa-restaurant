<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="site-header">
    <!-- Logo -->
    <div class="logo">
        <a href="<?php echo esc_url( home_url('/') ); ?>">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            } else {
                echo '<span style="font-family:\'Dancing Script\',cursive;font-size:24px;color:#5e4743;font-weight:700;">Ngon Thị Hoa</span>';
            }
            ?>
        </a>
    </div>

    <!-- Main Navigation (left side) -->
    <nav class="main-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'ngonthihoa' ); ?>">
        <a href="<?php echo esc_url( home_url('/') ); ?>">
            <?php esc_html_e( 'Trang Chủ', 'ngonthihoa' ); ?>
        </a>

        <!-- Menu dropdown -->
        <div class="dropdown">
            <span class="dropdown-toggle">
                <?php esc_html_e( 'Thực Đơn', 'ngonthihoa' ); ?> ▾
            </span>
            <div class="dropdown-menu">
                <?php
                $menu_groups = [
                    'sang'          => 'Sáng',
                    'trua_toi'      => 'Trưa & Tối',
                    'do_uong'       => 'Đồ Uống',
                    'do_uong_co_con'=> 'Đồ Uống Có Cồn',
                    'ruou_vang'     => 'Rượu Vang',
                ];
                $menu_page = get_page_by_path('menu');
                $menu_url  = $menu_page ? get_permalink($menu_page->ID) : home_url('/menu/');
                foreach ( $menu_groups as $slug => $label ) {
                    $url = ( $slug === 'ruou_vang' )
                        ? add_query_arg( 'group', $slug, $menu_url )
                        : add_query_arg( 'group', $slug, home_url('/') );
                    echo '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
                }
                ?>
            </div>
        </div>

        <!-- Media -->
        <?php
        $media_page = get_page_by_path('media');
        $media_url  = $media_page ? get_permalink($media_page->ID) : home_url('/media/');
        ?>
        <a href="<?php echo esc_url($media_url); ?>"><?php esc_html_e( 'Media', 'ngonthihoa' ); ?></a>

        <!-- News & Recruitment dropdown -->
        <div class="dropdown">
            <span class="dropdown-toggle">
                <?php esc_html_e( 'Tin Tức - Tuyển Dụng', 'ngonthihoa' ); ?> ▾
            </span>
            <div class="dropdown-menu">
                <a href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ?: home_url('/blog/') ); ?>">
                    <?php esc_html_e( 'Tin Tức', 'ngonthihoa' ); ?>
                </a>
                <?php
                $recruit_page = get_page_by_path('tuyen-dung');
                if ( $recruit_page ) {
                    echo '<a href="' . esc_url(get_permalink($recruit_page->ID)) . '">' . esc_html__('Tuyển Dụng','ngonthihoa') . '</a>';
                }
                ?>
            </div>
        </div>
    </nav>

    <!-- Right side: Language + CTAs -->
    <div class="header-right" style="display:flex;align-items:center;gap:16px;">
        <?php ngonthihoa_language_switcher(); ?>

        <button class="btn-contact" onclick="document.getElementById('contact-modal').classList.add('open')">
            <?php esc_html_e( 'Liên Hệ', 'ngonthihoa' ); ?>
        </button>
        <button class="btn-reserve" onclick="document.getElementById('reservation-modal').classList.add('open')">
            <?php esc_html_e( 'Đặt Bàn', 'ngonthihoa' ); ?>
        </button>
    </div>

    <!-- Mobile toggle -->
    <button class="mobile-menu-toggle" aria-label="Toggle menu" onclick="document.getElementById('mobile-nav').classList.toggle('open')">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
</header>

<!-- Mobile nav overlay -->
<nav id="mobile-nav" class="mobile-nav" role="navigation">
    <a href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e( 'Trang Chủ', 'ngonthihoa' ); ?></a>
    <?php
    foreach ( ['sang'=>'Sáng','trua_toi'=>'Trưa & Tối','do_uong'=>'Đồ Uống','do_uong_co_con'=>'Đồ Uống Có Cồn','ruou_vang'=>'Rượu Vang'] as $slug => $label ) {
        $url = ( $slug === 'ruou_vang' )
            ? add_query_arg( 'group', $slug, $menu_url )
            : add_query_arg( 'group', $slug, home_url('/') );
        echo '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
    }
    ?>
    <a href="<?php echo esc_url($media_url); ?>"><?php esc_html_e( 'Media', 'ngonthihoa' ); ?></a>
    <a href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ?: home_url('/blog/') ); ?>"><?php esc_html_e( 'Tin Tức', 'ngonthihoa' ); ?></a>
    <button class="btn-reserve" onclick="document.getElementById('reservation-modal').classList.add('open')">
        <?php esc_html_e( 'Đặt Bàn', 'ngonthihoa' ); ?>
    </button>
</nav>
