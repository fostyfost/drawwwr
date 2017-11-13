/**
 * lg-create-picture - v1.0.0 - 2017-03-18
 * Copyright (c) 2017 Fosty Fost; Licensed GPLv3
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function (jQuery) {
    (function () {
        'use strict';

        var defaults = {
            editPicture: false
        };

        /**
         * @param element
         * @returns {EditPicture}
         * @constructor
         */
        var EditPicture = function (element) {
            this.core = jQuery(element).data('lightGallery');

            this.core.s = jQuery.extend({}, defaults, this.core.s);

            this.core.s.editPicture = true;

            if (this.core.s.editPicture && this.core.doCss()) {
                this.init();
            }

            return this;
        };

        EditPicture.prototype.init = function () {
            var _ = this,
                editPictureIcon = '<a id="lg-edit-picture" class="lg-icon hidden"></a>',
                $editElement;

            _.core.$outer.find('.lg-toolbar').append(editPictureIcon);

            $editElement = jQuery('#lg-edit-picture');

            _.core.$el.on('onBeforeSlide.lg.drawwwr', function (event, prevIndex, index) {
                var $url = _.core.$items.eq(index).data('edit');

                if (!$url || $url === 'false' || $url === undefined || $url === '') {
                    $editElement.attr('href', '');

                    if (!$editElement.hasClass('hidden')) {
                        $editElement.addClass('hidden');
                    }
                } else {
                    $editElement.attr('href', $url);

                    if ($editElement.hasClass('hidden')) {
                        $editElement.removeClass('hidden');
                    }
                }
            });
        };

        /**
         * @returns {EditPicture}
         */
        EditPicture.prototype.destroy = function () {
            return this;
        };

        /**
         * @type {EditPicture}
         */
        jQuery.fn.lightGallery.modules.EditPicture = EditPicture;
    })();
}));

jQuery(function () {
    jQuery('#lightgallery').lightGallery({
        pager: true
    });
});
