<?php
/**
 * Template Name: Menu Page
 */
get_header();
?>

<main id="main-content">
    <div class="page-hero">
        <div class="page-hero-bg">
            <?php
            $hero_id = get_theme_mod('ngonthihoa_menu_hero');
            if ($hero_id) echo wp_get_attachment_image($hero_id,'ngonthihoa-hero');
            ?>
        </div>
        <div class="page-hero-content">
            <h1><?php esc_html_e( 'Thực Đơn', 'ngonthihoa' ); ?></h1>
            <p><?php esc_html_e( 'MENU | MENU | 菜单', 'ngonthihoa' ); ?></p>
        </div>
    </div>

    <section style="padding:60px 0;">
        <div class="container">
            <!-- VAT Notice -->
            <div style="text-align:center;margin-bottom:40px;font-size:13px;color:#888;letter-spacing:.05em;">
                Giá chưa bao gồm VAT &nbsp;|&nbsp; Prices do not include VAT &nbsp;|&nbsp; 价格不含增值税
            </div>

            <!-- Group Tabs -->
            <div class="menu-tabs" id="menu-page-tabs">
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

            <?php foreach ( $groups as $group_slug => $group_label ) : ?>
            <div id="group-<?php echo esc_attr($group_slug); ?>" class="menu-group-panel" style="<?php echo $group_slug !== $active_group ? 'display:none;' : ''; ?>">
                <h2 class="section-title" style="font-size:28px;margin-bottom:32px;"><?php echo esc_html($group_label); ?></h2>

                <?php
                // Get all subcategories within this group
                $terms = get_terms([
                    'taxonomy'   => 'menu_category',
                    'meta_query' => [[
                        'key'   => 'menu_group',
                        'value' => $group_slug,
                    ]],
                    'hide_empty' => false,
                ]);

                if ( ! empty($terms) && ! is_wp_error($terms) ) :
                    foreach ( $terms as $term ) :
                        $items_query = new WP_Query([
                            'post_type'      => 'menu_item',
                            'posts_per_page' => -1,
                            'tax_query'      => [[
                                'taxonomy' => 'menu_category',
                                'field'    => 'term_id',
                                'terms'    => $term->term_id,
                            ]],
                        ]);
                        if ( ! $items_query->have_posts() ) continue;
                        ?>
                        <div style="margin-bottom:48px;">
                            <h3 style="font-size:20px;color:#5e4743;border-bottom:2px solid #ffc952;padding-bottom:10px;margin-bottom:20px;"><?php echo esc_html($term->name); ?></h3>
                            <div class="menu-items-grid">
                                <?php while ($items_query->have_posts()) : $items_query->the_post();
                                    $price       = get_post_meta(get_the_ID(),'_menu_price',true);
                                    $price_shot  = get_post_meta(get_the_ID(),'_menu_price_shot',true);
                                    $price_bot   = get_post_meta(get_the_ID(),'_menu_price_bottle',true);
                                    $desc_vi     = get_post_meta(get_the_ID(),'_menu_description_vi',true);
                                    $desc_en     = get_post_meta(get_the_ID(),'_menu_description_en',true);
                                    ?>
                                    <div class="menu-item-card">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="menu-item-img"><?php the_post_thumbnail('ngonthihoa-thumb'); ?></div>
                                        <?php endif; ?>
                                        <div class="menu-item-body">
                                            <h4 class="menu-item-name"><?php the_title(); ?></h4>
                                            <?php if ($desc_en) : ?>
                                                <p class="menu-item-desc"><?php echo esc_html($desc_en); ?></p>
                                            <?php endif; ?>
                                            <?php if ($price_shot && $price_bot) : ?>
                                                <p class="menu-item-price" style="font-size:13px;">
                                                    Shot: <?php echo esc_html($price_shot); ?> đ &nbsp;/&nbsp; Chai: <?php echo esc_html($price_bot); ?> đ
                                                </p>
                                            <?php elseif ($price) : ?>
                                                <p class="menu-item-price"><?php echo esc_html($price); ?> đ</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endforeach; else :
                    // No subcategories — show items directly
                    $direct_query = new WP_Query([
                        'post_type'      => 'menu_item',
                        'posts_per_page' => -1,
                        'tax_query'      => [[
                            'taxonomy' => 'menu_group',
                            'field'    => 'slug',
                            'terms'    => $group_slug,
                        ]],
                    ]);
                    if ($direct_query->have_posts()) : ?>
                        <div class="menu-items-grid">
                            <?php while ($direct_query->have_posts()) : $direct_query->the_post();
                                $price = get_post_meta(get_the_ID(),'_menu_price',true);
                                ?>
                                <div class="menu-item-card">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="menu-item-img"><?php the_post_thumbnail('ngonthihoa-thumb'); ?></div>
                                    <?php endif; ?>
                                    <div class="menu-item-body">
                                        <h4 class="menu-item-name"><?php the_title(); ?></h4>
                                        <p class="menu-item-desc"><?php the_excerpt(); ?></p>
                                        <?php if ($price) : ?>
                                            <p class="menu-item-price"><?php echo esc_html($price); ?> đ</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    <?php endif; endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
