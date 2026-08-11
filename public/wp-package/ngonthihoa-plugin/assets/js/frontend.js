/* eslint-disable */
/* Ngon Thi Hoa - Frontend JS
   Handles reservation, contact, job application form submissions
   + GA4/Facebook Pixel event firing after successful submit
*/
(function($) {
    'use strict';

    // ── Helper: show message ───────────────────────────────────────────────
    function showMessage($form, type, text) {
        var $msg = $form.find('.nth-form-message');
        $msg.removeClass('nth-success nth-error').addClass(type === 'success' ? 'nth-success' : 'nth-error');
        $msg.text(text).show();
        $('html, body').animate({ scrollTop: $msg.offset().top - 80 }, 400);
    }

    // ── Helper: fire tracking events ──────────────────────────────────────
    function fireTrackingEvent(eventName, params) {
        // GA4
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, params || {});
        }
        // GTM dataLayer
        if (typeof window.dataLayer !== 'undefined') {
            window.dataLayer.push({ event: eventName });
        }
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            if (eventName === 'reservation_submitted') {
                fbq('track', 'Schedule');
            } else if (eventName === 'contact_submitted') {
                fbq('track', 'Contact');
            } else if (eventName === 'application_submitted') {
                fbq('track', 'SubmitApplication');
            }
        }
    }

    // ── Generic AJAX form handler ─────────────────────────────────────────
    function handleFormSubmit($form, action) {
        $form.on('submit', function(e) {
            e.preventDefault();
            var $btn = $form.find('[type="submit"]');
            var originalText = $btn.text();

            $btn.prop('disabled', true).text(nth_ajax.strings.submitting);
            $form.find('.nth-form-message').hide();

            var formData = new FormData(this);
            formData.append('action', action);
            formData.append('nonce', nth_ajax.nonce);
            formData.append('language', nth_ajax.lang);

            // reCAPTCHA v3 (if available)
            if (typeof grecaptcha !== 'undefined' && nth_ajax.recaptcha_site_key) {
                grecaptcha.ready(function() {
                    grecaptcha.execute(nth_ajax.recaptcha_site_key, { action: action }).then(function(token) {
                        formData.append('recaptcha_token', token);
                        doAjax();
                    });
                });
            } else {
                doAjax();
            }

            function doAjax() {
                $.ajax({
                    url: nth_ajax.ajax_url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showMessage($form, 'success', response.data.message);
                            $form[0].reset();
                            if (response.data.track_event) {
                                fireTrackingEvent(response.data.track_event, response.data.track_params);
                            }
                        } else {
                            showMessage($form, 'error', response.data.message || nth_ajax.strings.error_general);
                        }
                    },
                    error: function() {
                        showMessage($form, 'error', nth_ajax.strings.error_general);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            }
        });
    }

    // ── Init forms ────────────────────────────────────────────────────────
    $(document).ready(function() {
        if ($('#nth-reservation-form').length) {
            handleFormSubmit($('#nth-reservation-form'), 'nth_submit_reservation');
        }
        if ($('#nth-contact-form').length) {
            handleFormSubmit($('#nth-contact-form'), 'nth_submit_contact');
        }
        if ($('#nth-application-form').length) {
            handleFormSubmit($('#nth-application-form'), 'nth_submit_application');
        }

        // Set min date to today for reservation date input
        var $dateInput = $('[name="reservation_date"]');
        if ($dateInput.length) {
            var today = new Date().toISOString().split('T')[0];
            $dateInput.attr('min', today);
        }
    });

})(jQuery);
