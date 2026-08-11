<?php get_header(); ?>

<main id="main-content">
    <section style="padding:120px 0;text-align:center;">
        <div class="container">
            <h1 class="section-title"><?php esc_html_e( 'Ngon Thị Hoa', 'ngonthihoa' ); ?></h1>
            <p><?php esc_html_e( 'Welcome to Ngon Thi Hoa - Tropical Garden Restaurant.', 'ngonthihoa' ); ?></p>
        </div>
    </section>

    <?php if ( have_posts() ) : ?>
        <div class="container" style="padding-bottom:80px;">
            <div class="blog-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article class="blog-card">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="blog-card-img">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('ngonthihoa-card'); ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="blog-card-body">
                            <p class="blog-card-date"><?php echo get_the_date(); ?></p>
                            <h2 class="blog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p class="blog-card-excerpt"><?php the_excerpt(); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn-read-more"><?php esc_html_e( 'Read More →', 'ngonthihoa' ); ?></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
