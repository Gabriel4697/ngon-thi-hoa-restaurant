<div class="modal-overlay" id="reservation-modal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('reservation-modal').classList.remove('open')">✕</button>
        <h2 class="modal-title"><?php esc_html_e( 'Đặt Bàn', 'ngonthihoa' ); ?></h2>
        <p class="modal-subtitle"><?php esc_html_e( 'Vui lòng điền thông tin để đặt bàn', 'ngonthihoa' ); ?></p>

        <div id="reservation-success" class="notice notice-success" style="display:none;">
            <?php esc_html_e( 'Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận sớm nhất.', 'ngonthihoa' ); ?>
        </div>
        <div id="reservation-error" class="notice notice-error" style="display:none;"></div>

        <form id="reservation-form">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label><?php esc_html_e( 'Họ và tên *', 'ngonthihoa' ); ?></label>
                    <input type="text" name="name" required placeholder="<?php esc_attr_e( 'Nguyễn Văn A', 'ngonthihoa' ); ?>">
                </div>
                <div class="form-group">
                    <label><?php esc_html_e( 'Số điện thoại *', 'ngonthihoa' ); ?></label>
                    <input type="tel" name="phone" required placeholder="0912 345 678">
                </div>
            </div>
            <div class="form-group">
                <label><?php esc_html_e( 'Email', 'ngonthihoa' ); ?></label>
                <input type="email" name="email" placeholder="email@example.com">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label><?php esc_html_e( 'Ngày đặt bàn *', 'ngonthihoa' ); ?></label>
                    <input type="date" name="date" required>
                </div>
                <div class="form-group">
                    <label><?php esc_html_e( 'Giờ *', 'ngonthihoa' ); ?></label>
                    <select name="time" required>
                        <option value=""><?php esc_html_e( 'Chọn giờ', 'ngonthihoa' ); ?></option>
                        <?php
                        for ( $h = 7; $h <= 21; $h++ ) {
                            foreach ( ['00','30'] as $m ) {
                                printf( '<option value="%02d:%s">%02d:%s</option>', $h, $m, $h, $m );
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label><?php esc_html_e( 'Số lượng khách *', 'ngonthihoa' ); ?></label>
                <select name="guests" required>
                    <option value=""><?php esc_html_e( 'Chọn số khách', 'ngonthihoa' ); ?></option>
                    <?php for ($g = 1; $g <= 20; $g++) echo "<option value='$g'>$g " . esc_html__('khách','ngonthihoa') . '</option>'; ?>
                    <option value="20+"><?php esc_html_e( 'Trên 20 khách', 'ngonthihoa' ); ?></option>
                </select>
            </div>
            <div class="form-group">
                <label><?php esc_html_e( 'Ghi chú', 'ngonthihoa' ); ?></label>
                <textarea name="notes" placeholder="<?php esc_attr_e( 'Yêu cầu đặc biệt, dị ứng thực phẩm...', 'ngonthihoa' ); ?>"></textarea>
            </div>
            <button type="submit" class="btn-submit"><?php esc_html_e( 'Xác Nhận Đặt Bàn', 'ngonthihoa' ); ?></button>
        </form>
    </div>
</div>

<script>
document.getElementById('reservation-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var data = new FormData(this);
    data.append('action', 'ngonthihoa_reservation');
    data.append('nonce', ngonthihoaVars.nonce);
    document.getElementById('reservation-error').style.display = 'none';

    fetch(ngonthihoaVars.ajaxUrl, { method:'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                document.getElementById('reservation-success').style.display = 'block';
                document.getElementById('reservation-form').reset();
            } else {
                var err = document.getElementById('reservation-error');
                err.textContent = res.data.message || 'Có lỗi xảy ra.';
                err.style.display = 'block';
            }
        });
});
</script>
