<?php
// Modals
get_template_part( 'template-parts/modal', 'reservation' );
get_template_part( 'template-parts/modal', 'contact' );
?>

<footer id="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand -->
            <div>
                <div class="footer-logo">
                    <?php if ( has_custom_logo() ) {
                        the_custom_logo();
                    } else {
                        echo '<span style="font-family:\'Dancing Script\',cursive;font-size:28px;color:#ffc952;font-weight:700;">Ngon Thị Hoa</span>';
                    } ?>
                </div>
                <p class="footer-desc">
                    <?php esc_html_e( 'Nhà hàng Ngon Thị Hoa - Tropical Garden, nơi hội tụ tinh hoa ẩm thực Việt Nam giữa không gian xanh mát tại Đà Nẵng.', 'ngonthihoa' ); ?>
                </p>
                <div style="display:flex;gap:12px;">
                    <a href="<?php echo esc_url( get_theme_mod('ngonthihoa_facebook','https://fb.me/ngonthihoa') ); ?>" target="_blank" rel="noopener" style="color:rgba(255,255,255,0.6);">Facebook</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="footer-heading"><?php esc_html_e( 'Liên Kết Nhanh', 'ngonthihoa' ); ?></h4>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e( 'Trang Chủ', 'ngonthihoa' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/menu/') ); ?>"><?php esc_html_e( 'Thực Đơn', 'ngonthihoa' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/media/') ); ?>"><?php esc_html_e( 'Media', 'ngonthihoa' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/blog/') ); ?>"><?php esc_html_e( 'Tin Tức', 'ngonthihoa' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/tuyen-dung/') ); ?>"><?php esc_html_e( 'Tuyển Dụng', 'ngonthihoa' ); ?></a></li>
                </ul>
            </div>

            <!-- Menu Groups -->
            <div>
                <h4 class="footer-heading"><?php esc_html_e( 'Thực Đơn', 'ngonthihoa' ); ?></h4>
                <ul class="footer-links">
                    <?php
                    $groups = ['sang'=>'Sáng','trua_toi'=>'Trưa & Tối','do_uong'=>'Đồ Uống','do_uong_co_con'=>'Đồ Uống Có Cồn','ruou_vang'=>'Rượu Vang'];
                    foreach ( $groups as $slug => $label ) {
                        $url = add_query_arg( 'group', $slug, home_url('/menu/') );
                        echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="footer-heading"><?php esc_html_e( 'Liên Hệ', 'ngonthihoa' ); ?></h4>
                <div class="footer-contact">
                    <p>📍 <?php echo esc_html( get_theme_mod('ngonthihoa_address', '100 Lê Quang Đạo, Ngũ Hành Sơn, Đà Nẵng') ); ?></p>
                    <p>📞 <a href="tel:<?php echo esc_attr( get_theme_mod('ngonthihoa_phone1','02366515100') ); ?>"><?php echo esc_html( get_theme_mod('ngonthihoa_phone1','02366 515 100') ); ?></a></p>
                    <p>📞 <a href="tel:<?php echo esc_attr( get_theme_mod('ngonthihoa_phone2','0967220100') ); ?>"><?php echo esc_html( get_theme_mod('ngonthihoa_phone2','0967 220 100') ); ?></a></p>
                    <p>✉️ <a href="mailto:info@ngonthihoarestaurant.com">info@ngonthihoarestaurant.com</a></p>
                    <p>🌐 <a href="https://ngonthihoarestaurant.com" target="_blank">ngonthihoarestaurant.com</a></p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© <?php echo date('Y'); ?> Ngon Thị Hoa – Tropical Garden. <?php esc_html_e( 'All rights reserved.', 'ngonthihoa' ); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
