/* eslint-disable */
/* Ngon Thi Hoa - Admin JS */
(function($) {
    'use strict';

    $(document).ready(function() {

        // ── Status select → AJAX update ───────────────────────────────────
        $(document).on('change', '.nth-status-select', function() {
            var $select = $(this);
            var id      = $select.data('id');
            var type    = $select.data('type');
            var status  = $select.val();
            var action  = 'nth_update_' + type + '_status';

            $.post(nth_admin.ajax_url, {
                action: action,
                nonce:  nth_admin.nonce,
                id:     id,
                status: status
            }, function(response) {
                if (response.success) {
                    var $row = $select.closest('tr');
                    $row.css('background', '#f0fdf4');
                    setTimeout(function() { $row.css('background', ''); }, 1200);
                }
            });
        });

        // ── Mark contact as read ──────────────────────────────────────────
        $(document).on('click', '.nth-mark-read-btn', function() {
            var $btn = $(this);
            var id   = $btn.data('id');

            $.post(nth_admin.ajax_url, {
                action: 'nth_update_contact_status',
                nonce:  nth_admin.nonce,
                id:     id,
                status: 'read'
            }, function(response) {
                if (response.success) {
                    var $row = $btn.closest('tr');
                    $row.find('.nth-badge').removeClass('nth-badge-unread').addClass('nth-badge-read').text('Đã đọc');
                    $btn.remove();
                }
            });
        });

        // ── Delete record ──────────────────────────────────────────────────
        $(document).on('click', '.nth-delete-btn', function() {
            var $btn = $(this);
            if (!confirm('Bạn có chắc muốn xóa mục này không?')) return;

            var id   = $btn.data('id');
            var type = $btn.data('type');

            $.post(nth_admin.ajax_url, {
                action: 'nth_delete_record',
                nonce:  nth_admin.nonce,
                id:     id,
                type:   type
            }, function(response) {
                if (response.success) {
                    $btn.closest('tr').fadeOut(300, function() { $(this).remove(); });
                }
            });
        });

        // ── Media uploader ────────────────────────────────────────────────
        var mediaFrame;
        $(document).on('click', '.nth-media-upload', function(e) {
            e.preventDefault();
            var $btn    = $(this);
            var target  = $btn.data('target');
            var $target = $('#' + target);

            if (mediaFrame) { mediaFrame.open(); return; }

            mediaFrame = wp.media({
                title:    'Chọn hình ảnh',
                button:   { text: 'Chọn ảnh này' },
                multiple: false
            });

            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                $target.val(attachment.url);
                $target.prev('img').remove();
                $target.before('<img src="' + attachment.url + '" style="max-width:200px;display:block;margin-bottom:8px;" />');
            });

            mediaFrame.open();
        });

    });

})(jQuery);
