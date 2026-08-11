<div class="modal-overlay" id="contact-modal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('contact-modal').classList.remove('open')">✕</button>
        <h2 class="modal-title"><?php esc_html_e( 'Liên Hệ', 'ngonthihoa' ); ?></h2>
        <p class="modal-subtitle"><?php esc_html_e( 'Gửi tin nhắn cho chúng tôi', 'ngonthihoa' ); ?></p>

        <div id="contact-success" class="notice notice-success" style="display:none;">
            <?php esc_html_e( 'Tin nhắn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất.', 'ngonthihoa' ); ?>
        </div>

        <form id="contact-form">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label><?php esc_html_e( 'Họ và tên *', 'ngonthihoa' ); ?></label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label><?php esc_html_e( 'Email *', 'ngonthihoa' ); ?></label>
                    <input type="email" name="email" required>
                </div>
            </div>
            <div class="form-group">
                <label><?php esc_html_e( 'Số điện thoại', 'ngonthihoa' ); ?></label>
                <input type="tel" name="phone">
            </div>
            <div class="form-group">
                <label><?php esc_html_e( 'Tiêu đề', 'ngonthihoa' ); ?></label>
                <input type="text" name="subject">
            </div>
            <div class="form-group">
                <label><?php esc_html_e( 'Nội dung *', 'ngonthihoa' ); ?></label>
                <textarea name="message" required rows="5"></textarea>
            </div>
            <button type="submit" class="btn-submit"><?php esc_html_e( 'Gửi Tin Nhắn', 'ngonthihoa' ); ?></button>
        </form>
    </div>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var data = new FormData(this);
    data.append('action', 'ngonthihoa_contact');
    data.append('nonce', ngonthihoaVars.nonce);

    fetch(ngonthihoaVars.ajaxUrl, { method:'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                document.getElementById('contact-success').style.display = 'block';
                document.getElementById('contact-form').reset();
            }
        });
});
</script>
