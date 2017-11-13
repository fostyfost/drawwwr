/**
 * @param action
 * @returns {jQuery}
 */
jQuery.fn.preloader = function (action) {
    if (actions[action] && typeof actions[action] === 'function') {
        actions[action].apply(this);
    }

    return this;
};

(function (jQuery) {
    /**
     * @type {{start: start, stop: stop}}
     */
    var actions = {
        start: function () {
            this.$preloaderInner = jQuery('<div></div>')
                .addClass('loader-inner')
                .addClass('ball-scale-multiple')
                .loaders();

            this.$preloader = jQuery('<div></div>')
                .addClass('preloader')
                .append(this.$preloaderInner);

            this.$preloaderOverlay = jQuery('<div id="js__preloader"></div>')
                .addClass('preloader-overlay')
                .append(this.$preloader);

            this.append(this.$preloaderOverlay);
        },

        stop: function () {
            this.find('.preloader-overlay').remove();
        }
    };

    /**
     * @param action
     * @returns {jQuery}
     */
    jQuery.fn.preloader = function (action) {
        actions[action].apply(this);

        return this;
    };
}(jQuery));

(function (jQuery) {
    'use strict';

    jQuery(function () {
        var $body = jQuery('body'),
            $form = jQuery('#ff_auth_form'),
            $password = jQuery('#ff_auth_password'),
            wrongPassword,
            incorrectPasswordLength,
            formData = '',
            $tips = jQuery('#ff_auth_error_message'),
            valid = true;

        if (window.drawwwrData !== undefined) {
            if (window.drawwwrData.incorrectPasswordLength !== undefined) {
                incorrectPasswordLength = window.drawwwrData.incorrectPasswordLength;
            }

            if (window.drawwwrData.wrongPassword !== undefined) {
                wrongPassword = window.drawwwrData.wrongPassword;
            }
        }

        /**
         * @private
         */
        function _setWrongPasswordError () {
            if (wrongPassword === '' || wrongPassword === undefined) {
                wrongPassword = 'Wrong password';
            }

            _updateTips(wrongPassword);

            setTimeout(function () {
                $body.preloader('stop');
            }, 1000);
        }

        /**
         * @private
         */
        function _setUnexpectedError () {
            switch (true) {
                case window.drawwwrData === undefined:
                    _updateTips('Unexpected error');
                    break;

                case window.drawwwrData.unexpectedError === undefined:
                    _updateTips('Unexpected error');
                    break;

                default:
                    _updateTips(window.drawwwrData.unexpectedError);
            }
        }

        /**
         * @param t
         * @private
         */
        function _updateTips (t) {
            $tips
                .text(t)
                .addClass('ff-auth__error-message--visible');
        }

        /**
         * @param o
         * @param min
         * @param max
         * @returns {boolean}
         * @private
         */
        function _checkPasswordLength (o, min, max) {
            if (o.val().length > max || o.val().length < min) {
                if (incorrectPasswordLength === '' || incorrectPasswordLength === undefined) {
                    incorrectPasswordLength = 'Length must be between ' + min + ' and ' + max;
                }

                _updateTips(incorrectPasswordLength);

                return false;
            }

            return true;
        }

        /**
         * @private
         */
        function _decrypt () {
            try {
                return CryptoJS.AES
                    .decrypt(localStorage.getItem('ENCRYPTED_H5I'), $password.val())
                    .toString(CryptoJS.enc.Utf8);
            } catch (error) {
                _setWrongPasswordError();

                console.warn(error);
            }
        }

        $password.on('input', function () {
            $tips.removeClass('ff-auth__error-message--visible');
        });

        /**
         * @private
         */
        function _enable () {
            setTimeout(function () {
                $password.prop('disabled', false);
            }, 100);
        }

        /**
         * @param data
         * @param textStatus
         * @param jqXHR
         * @private
         */
        function _success (data, textStatus, jqXHR) {
            switch (true) {
                case data.status === undefined:
                    _setUnexpectedError();

                    _enable();

                    setTimeout(function () {
                        $body.preloader('stop');
                    }, 1000);

                    break;

                case data.status === 'error':
                    if (data.text === undefined) {
                        _setUnexpectedError();
                    } else {
                        _updateTips(data.text);
                    }

                    _enable();

                    setTimeout(function () {
                        $body.preloader('stop');
                    }, 1000);

                    break;

                case data.status === 'success':
                    setTimeout(function () {
                        location.reload();
                    }, 1000);

                    break;
            }

            console.log({
                'data': data,
                'textStatus': textStatus,
                'jqXHR': jqXHR
            });
        }

        /**
         * @param jqXHR
         * @param textStatus
         * @param errorThrown
         * @private
         */
        function _error (jqXHR, textStatus, errorThrown) {
            _setUnexpectedError();

            _enable();

            setTimeout(function () {
                $body.preloader('stop');
            }, 1000);

            console.error({
                'jqXHR': jqXHR,
                'textStatus': textStatus,
                'errorThrown': errorThrown
            });
        }

        $form.on('submit', function (event) {
            event.preventDefault();

            _enable();
        });

        jQuery('#ff_auth_submit_button').on('click', function (event) {
            var decrypted = _decrypt();

            $body.preloader('start');

            event.preventDefault();

            valid = _checkPasswordLength($password, 5, 16);

            if (!valid) {
                _enable();

                return false;
            }

            if (/data:/.test(decrypted)) {
                localStorage.setItem('ENCRYPTED_H5I', '');
                localStorage.setItem('DECRYPTED_H5I', decrypted);
            } else {
                _setWrongPasswordError();

                return false;
            }

            setTimeout(function () {
                try {
                    formData = $form.serialize();

                    jQuery.ajax({
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        url: window.drawwwrData.ajaxPath,
                        success: _success,
                        error: _error
                    });
                } catch (error) {
                    setTimeout(function () {
                        $body.preloader('stop');
                    }, 1000);

                    console.error('Catch', error);
                }
            }, 100);

            return valid;
        });
    });
})(jQuery);

/**
 * @returns {boolean}
 */
function goBack () {
    if (
        window.history.length > 1
        && document.referrer !== window.location.href
        && document.referrer.split('/')[2] === window.location.host
    ) {
        window.history.back();
    } else {
        window.location.href = '/';
    }

    return false;
}
