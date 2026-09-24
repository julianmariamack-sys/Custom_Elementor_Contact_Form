(function () {
    'use strict';

    function initForm(form) {
        if (!form || form.dataset.cecfReady === '1') {
            return;
        }

        form.dataset.cecfReady = '1';

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var response = form.querySelector('.cecf-response');
            var button = form.querySelector('.cecf-button');
            var buttonText = form.querySelector('.cecf-button-text');
            var originalText = buttonText ? buttonText.textContent : '';

            response.className = 'cecf-response';
            response.textContent = '';

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            form.classList.add('is-sending');
            button.disabled = true;

            if (buttonText) {
                buttonText.textContent = (window.CECF_DATA && CECF_DATA.sending) ? CECF_DATA.sending : 'Sending…';
            }

            var formData = new FormData(form);

            fetch((window.CECF_DATA && CECF_DATA.ajaxUrl) ? CECF_DATA.ajaxUrl : '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (!data || !data.success) {
                    throw new Error(data && data.data && data.data.message ? data.data.message : 'Something went wrong.');
                }

                response.classList.add('is-success');
                response.textContent = data.data && data.data.message
                    ? data.data.message
                    : (form.querySelector('[name="success_message"]') || {}).value || 'Thank you! Your message has been sent.';

                form.reset();
            })
            .catch(function (error) {
                response.classList.add('is-error');
                response.textContent = error.message || ((window.CECF_DATA && CECF_DATA.error) ? CECF_DATA.error : 'Something went wrong. Please try again.');
            })
            .finally(function () {
                form.classList.remove('is-sending');
                button.disabled = false;

                if (buttonText) {
                    buttonText.textContent = originalText;
                }
            });
        });
    }

    function scan() {
        document.querySelectorAll('.cecf-form').forEach(initForm);
    }

    document.addEventListener('DOMContentLoaded', scan);

    // Elementor editor support: widgets can be inserted/re-rendered without a full page reload.
    if (window.jQuery && window.elementorFrontend) {
        jQuery(window).on('elementor/frontend/init', function () {
            if (window.elementorFrontend && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/custom-elementor-contact-form.default', function ($scope) {
                    var form = $scope[0] ? $scope[0].querySelector('.cecf-form') : null;
                    initForm(form);
                });
            }
        });
    }
})();
