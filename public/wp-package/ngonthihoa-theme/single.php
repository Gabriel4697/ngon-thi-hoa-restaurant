<?php get_header(); ?>

<main id="main-content">
    <!-- Page Hero -->
    <div class="page-hero" style="height:280px;">
        <div class="page-hero-bg">
            <?php if ( has_post_thumbnail() ) { the_post_thumbnail('ngonthihoa-hero'); } ?>
        </div>
        <div class="page-hero-content">
            <h1 style="font-size:clamp(24px,3vw,40px);"><?php esc_html_e( 'Tin Tức', 'ngonthihoa' ); ?></h1>
        </div>
    </div>

    <div class="container" style="padding:60px 20px;display:grid;grid-template-columns:1fr 320px;gap:48px;align-items:start;">
        <!-- Main Article -->
        <article class="single-post" style="padding:0;">
            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                <div class="single-post-meta">
                    <span><?php echo get_the_date('d/m/Y'); ?></span>
                    <span>•</span>
                    <span><?php the_author(); ?></span>
                    <?php
                    $cats = get_the_category();
                    if ( $cats ) {
                        echo '<span>•</span><span>' . esc_html($cats[0]->name) . '</span>';
                    }
                    ?>
                </div>
                <h1 class="section-title" style="font-size:clamp(24px,3.5vw,40px);margin-bottom:24px;"><?php the_title(); ?></h1>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="single-post-img"><?php the_post_thumbnail('ngonthihoa-blog'); ?></div>
                <?php endif; ?>
                <div class="single-post-content"><?php the_content(); ?></div>

                <div style="margin-top:40px;text-align:center;">
                    <button class="btn-reserve" onclick="document.getElementById('reservation-modal').classList.add('open')">
                        <?php esc_html_e( 'Đặt Bàn Ngay', 'ngonthihoa' ); ?>
                    </button>
                </div>

                <!-- Related posts -->
                <?php
                $related = new WP_Query([
                    'post_type'      => 'post',
                    'posts_per_page' => 3,
                    'post__not_in'   => [get_the_ID()],
                    'category__in'   => wp_get_post_categories(get_the_ID()),
                ]);
                if ( $related->have_posts() ) :
                ?>
                <div style="margin-top:60px;">
                    <h3 class="section-title" style="font-size:24px;margin-bottom:24px;"><?php esc_html_e( 'Bài Viết Liên Quan', 'ngonthihoa' ); ?></h3>
                    <div class="blog-grid" style="grid-template-columns:repeat(3,1fr);">
                        <?php while ($related->have_posts()) : $related->the_post(); ?>
                            <article class="blog-card">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div class="blog-card-img">
                                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('ngonthihoa-card'); ?></a>
                                    </div>
                                <?php endif; ?>
                                <div class="blog-card-body">
                                    <p class="blog-card-date"><?php echo get_the_date('d/m/Y'); ?></p>
                                    <h4 class="blog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <a href="<?php the_permalink(); ?>" class="btn-read-more"><?php esc_html_e('Xem thêm →','ngonthihoa'); ?></a>
                                </div>
                            </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
                <?php endif; ?>

            <?php endwhile; ?>
        </article>

        <!-- Sidebar -->
        <aside>
            <?php if ( is_active_sidebar('blog-sidebar') ) {
                dynamic_sidebar('blog-sidebar');
            } else { ?>
                <!-- Latest Posts Widget -->
                <div class="widget" style="background:var(--brand-cream);padding:24px;border-radius:12px;margin-bottom:24px;">
                    <h3 style="font-family:'Playfair Display',serif;color:#5e4743;margin-bottom:16px;"><?php esc_html_e('Bài viết mới nhất','ngonthihoa'); ?></h3>
                    <?php
                    $recent = new WP_Query(['post_type'=>'post','posts_per_page'=>4,'post__not_in'=>[get_the_ID()]]);
                    while ($recent->have_posts()) : $recent->the_post(); ?>
                        <div style="display:flex;gap:12px;margin-bottom:16px;">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div style="width:64px;height:64px;border-radius:6px;overflow:hidden;flex-shrink:0;">
                                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                                </div>
                            <?php endif; ?>
                            <div>
                                <a href="<?php the_permalink(); ?>" style="font-size:14px;font-weight:600;color:#5e4743;line-height:1.4;"><?php the_title(); ?></a>
                                <p style="font-size:12px;color:#999;margin-top:4px;"><?php echo get_the_date('d/m/Y'); ?></p>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>

                <!-- Reserve CTA -->
                <div style="background:#5e4743;padding:24px;border-radius:12px;text-align:center;color:#fff;">
                    <h3 style="font-family:'Dancing Script',cursive;font-size:28px;color:#ffc952;margin-bottom:12px;">Đặt Bàn Ngay</h3>
                    <p style="font-size:14px;opacity:.8;margin-bottom:20px;">Liên hệ: 02366 515 100</p>
                    <button class="btn-reserve" onclick="document.getElementById('reservation-modal').classList.add('open')">
                        <?php esc_html_e('Đặt Bàn','ngonthihoa'); ?>
                    </button>
                </div>
            <?php } ?>
        </aside>
    </div>
</main>

<?php get_footer(); ?>
