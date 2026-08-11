<?php
/**
 * Template Name: Recruitment Page
 */
get_header();
?>

<main id="main-content">
    <div class="page-hero">
        <div class="page-hero-content">
            <h1><?php esc_html_e( 'Tuyển Dụng', 'ngonthihoa' ); ?></h1>
            <p><?php esc_html_e( 'JOIN OUR TEAM', 'ngonthihoa' ); ?></p>
        </div>
    </div>

    <section style="padding:60px 0;">
        <div class="container">
            <?php
            $jobs_query = new WP_Query([
                'post_type'      => 'job_position',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);

            if ( $jobs_query->have_posts() ) :
                while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
                    $requirements = get_post_meta(get_the_ID(), '_job_requirements', true);
                    $salary       = get_post_meta(get_the_ID(), '_job_salary', true);
                    $type         = get_post_meta(get_the_ID(), '_job_type', true) ?: 'Full-time';
                    ?>
                    <div class="job-card">
                        <h2 class="job-title"><?php the_title(); ?></h2>
                        <div class="job-meta">
                            <span class="job-tag"><?php echo esc_html($type); ?></span>
                            <?php if ($salary) : ?>
                                <span class="job-tag"><?php echo esc_html($salary); ?></span>
                            <?php endif; ?>
                            <span class="job-tag"><?php echo get_the_date('d/m/Y'); ?></span>
                        </div>
                        <div class="job-desc"><?php the_content(); ?></div>
                        <?php if ($requirements) : ?>
                            <div style="background:var(--brand-cream);border-radius:8px;padding:16px;margin-bottom:16px;">
                                <h4 style="font-size:14px;font-weight:700;color:#5e4743;margin-bottom:8px;">
                                    <?php esc_html_e('Yêu Cầu:','ngonthihoa'); ?>
                                </h4>
                                <p style="font-size:14px;color:#666;line-height:1.6;"><?php echo nl2br(esc_html($requirements)); ?></p>
                            </div>
                        <?php endif; ?>
                        <button class="btn-apply" onclick="openApplicationForm(<?php echo get_the_ID(); ?>, '<?php echo esc_js(get_the_title()); ?>')">
                            <?php esc_html_e('Ứng Tuyển Ngay','ngonthihoa'); ?>
                        </button>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else : ?>
                <div style="text-align:center;padding:80px 20px;color:#999;">
                    <p style="font-size:20px;"><?php esc_html_e('Hiện tại chưa có vị trí tuyển dụng.','ngonthihoa'); ?></p>
                    <p style="font-size:14px;margin-top:8px;"><?php esc_html_e('Vui lòng quay lại sau.','ngonthihoa'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- Job Application Modal -->
<div class="modal-overlay" id="application-modal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('application-modal').classList.remove('open')">✕</button>
        <h2 class="modal-title"><?php esc_html_e('Ứng Tuyển','ngonthihoa'); ?></h2>
        <p class="modal-subtitle" id="app-modal-job-title"></p>

        <div id="app-success" class="notice notice-success" style="display:none;">
            <?php esc_html_e('Ứng tuyển thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.','ngonthihoa'); ?>
        </div>

        <form id="application-form" enctype="multipart/form-data">
            <input type="hidden" id="app-job-id" name="job_id" value="">
            <div class="form-group">
                <label><?php esc_html_e('Họ và tên *','ngonthihoa'); ?></label>
                <input type="text" name="name" required placeholder="<?php esc_attr_e('Nguyễn Văn A','ngonthihoa'); ?>">
            </div>
            <div class="form-group">
                <label><?php esc_html_e('Email *','ngonthihoa'); ?></label>
                <input type="email" name="email" required placeholder="email@example.com">
            </div>
            <div class="form-group">
                <label><?php esc_html_e('Số điện thoại *','ngonthihoa'); ?></label>
                <input type="tel" name="phone" required placeholder="0912 345 678">
            </div>
            <div class="form-group">
                <label><?php esc_html_e('CV / Portfolio (PDF, DOC)','ngonthihoa'); ?></label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx">
            </div>
            <div class="form-group">
                <label><?php esc_html_e('Thư giới thiệu','ngonthihoa'); ?></label>
                <textarea name="letter" placeholder="<?php esc_attr_e('Giới thiệu về bản thân...','ngonthihoa'); ?>"></textarea>
            </div>
            <button type="submit" class="btn-submit"><?php esc_html_e('Gửi Ứng Tuyển','ngonthihoa'); ?></button>
        </form>
    </div>
</div>

<script>
function openApplicationForm(jobId, jobTitle) {
    document.getElementById('app-job-id').value = jobId;
    document.getElementById('app-modal-job-title').textContent = jobTitle;
    document.getElementById('application-modal').classList.add('open');
    document.getElementById('app-success').style.display = 'none';
    document.getElementById('application-form').reset();
}

document.getElementById('application-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('action', 'ngonthihoa_application');
    formData.append('nonce', ngonthihoaVars.nonce);

    fetch(ngonthihoaVars.ajaxUrl, { method:'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('app-success').style.display = 'block';
                document.getElementById('application-form').reset();
            }
        });
});
</script>

<?php get_footer(); ?>
