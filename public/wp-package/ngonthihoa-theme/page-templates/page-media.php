<?php
/**
 * Template Name: Media Gallery Page
 */
get_header();
?>

<main id="main-content">
    <div class="page-hero">
        <div class="page-hero-content">
            <h1>Media</h1>
            <p><?php esc_html_e( 'Hình Ảnh & Video', 'ngonthihoa' ); ?></p>
        </div>
    </div>

    <section style="padding:60px 0;">
        <div class="container">
            <!-- Filter Buttons -->
            <div class="menu-tabs" style="margin-bottom:40px;">
                <button class="menu-tab active" data-filter="all"><?php esc_html_e('Tất Cả','ngonthihoa'); ?></button>
                <button class="menu-tab" data-filter="image"><?php esc_html_e('Hình Ảnh','ngonthihoa'); ?></button>
                <button class="menu-tab" data-filter="video"><?php esc_html_e('Video','ngonthihoa'); ?></button>
            </div>

            <?php
            $media_query = new WP_Query([
                'post_type'      => 'media_item',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);
            ?>

            <div class="media-grid" id="media-gallery-grid">
                <?php
                $i = 0;
                if ( $media_query->have_posts() ) :
                    while ( $media_query->have_posts() ) : $media_query->the_post();
                        $type     = get_post_meta(get_the_ID(), '_media_type', true) ?: 'image';
                        $video    = get_post_meta(get_the_ID(), '_media_video_url', true);
                        $caption  = get_post_meta(get_the_ID(), '_media_caption', true) ?: get_the_title();
                        $img_url  = get_the_post_thumbnail_url(get_the_ID(), 'ngonthihoa-square');
                        ?>
                        <div class="media-item" data-type="<?php echo esc_attr($type); ?>" data-index="<?php echo $i; ?>" onclick="openLightbox(<?php echo $i; ?>)">
                            <?php if ( $type === 'video' && $video ) : ?>
                                <video src="<?php echo esc_url($video); ?>" muted loop poster="<?php echo esc_url($img_url); ?>"></video>
                            <?php elseif ( $img_url ) : ?>
                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($caption); ?>" loading="lazy">
                            <?php endif; ?>
                            <div class="media-item-overlay">
                                <?php echo $type === 'video' ? '▶' : '⊕'; ?>
                            </div>
                        </div>
                        <?php
                        $i++;
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>

            <?php if ( ! $media_query->have_posts() ) : ?>
                <p style="text-align:center;color:#999;padding:60px 0;"><?php esc_html_e('Chưa có nội dung media.','ngonthihoa'); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Lightbox -->
    <div class="lightbox" id="media-lightbox">
        <button class="lightbox-close" onclick="closeLightbox()">✕</button>
        <button class="lightbox-prev" onclick="prevMedia()">❮</button>
        <div class="lightbox-content" id="lightbox-content"></div>
        <button class="lightbox-next" onclick="nextMedia()">❯</button>
        <div style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.7);font-size:14px;" id="lightbox-caption"></div>
    </div>
</main>

<script>
// Build media data from PHP
var mediaData = <?php
    $media_query2 = new WP_Query(['post_type'=>'media_item','posts_per_page'=>-1,'post_status'=>'publish']);
    $data = [];
    while ($media_query2->have_posts()) {
        $media_query2->the_post();
        $data[] = [
            'type'    => get_post_meta(get_the_ID(),'_media_type',true) ?: 'image',
            'src'     => get_the_post_thumbnail_url(get_the_ID(),'ngonthihoa-hero') ?: '',
            'video'   => get_post_meta(get_the_ID(),'_media_video_url',true) ?: '',
            'caption' => get_post_meta(get_the_ID(),'_media_caption',true) ?: get_the_title(),
        ];
    }
    wp_reset_postdata();
    echo json_encode($data);
?>;

var currentIndex = 0;

function openLightbox(i) {
    currentIndex = i;
    renderLightbox();
    document.getElementById('media-lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('media-lightbox').classList.remove('open');
    document.body.style.overflow = '';
}

function prevMedia() { currentIndex = (currentIndex - 1 + mediaData.length) % mediaData.length; renderLightbox(); }
function nextMedia() { currentIndex = (currentIndex + 1) % mediaData.length; renderLightbox(); }

function renderLightbox() {
    var item = mediaData[currentIndex];
    var html = '';
    if (item.type === 'video' && item.video) {
        html = '<video src="' + item.video + '" controls autoplay style="max-width:90vw;max-height:80vh;border-radius:4px;"></video>';
    } else if (item.src) {
        html = '<img src="' + item.src + '" alt="' + item.caption + '">';
    }
    document.getElementById('lightbox-content').innerHTML = html;
    document.getElementById('lightbox-caption').textContent = item.caption + ' (' + (currentIndex+1) + '/' + mediaData.length + ')';
}

document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('media-lightbox');
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft')  prevMedia();
    if (e.key === 'ArrowRight') nextMedia();
});

// Filter
document.querySelectorAll('[data-filter]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        var filter = btn.dataset.filter;
        document.querySelectorAll('.media-item').forEach(function(item) {
            item.style.display = (filter === 'all' || item.dataset.type === filter) ? '' : 'none';
        });
    });
});
</script>

<?php get_footer(); ?>
