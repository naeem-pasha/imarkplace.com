/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    $.widget('mage.sticky', {
        options: {
            /**
             * Element selector, who's height will be used to restrict the
             * maximum offsetTop position of the stuck element.
             * Default uses document body.
             * @type {String}
             */
            container: '',

            /**
             * Spacing in pixels above the stuck element
             * @type {Number|Function} Number or Function that will return a Number
             */
            spacingTop: 0,

            /**
             * Allows postponing sticking, until element will go out of the
             * screen for the number of pixels.
             * @type {Number|Function} Number or Function that will return a Number
             */
            stickAfter: 0,

            /**
             * CSS class for active sticky state
             * @type {String}
             */
            stickyClass: '_sticky'
        },

        /**
         * Retrieve option value
         * @param  {String} option
         * @return {*}
         * @private
         */
        _getOptionValue: function (option) {
            var value = this.options[option] || 0;

            if (typeof value === 'function') {
                value = this.options[option]();
            }

            return value;
        },

        /**
         * Bind handlers to scroll event
         * @private
         */
        _create: function () {
            $(window).on({
                'scroll': $.proxy(this._stick, this),
                'resize': $.proxy(this.reset, this)
            });

            this.element.on('dimensionsChanged', $.proxy(this.reset, this));

            this.reset();
        },

        /**
         * float Block on windowScroll
         * @private
         */
        _stick: function () {
            var offset,
                isStatic,
                stuck,
                stickAfter;

            isStatic = this.element.css('position') === 'static';

            if (!isStatic && this.element.is(':visible')) {
                offset = $(document).scrollTop() -
                    this.parentOffset +
                    this._getOptionValue('spacingTop');

                offset = Math.max(0, Math.min(offset, this.maxOffset));

                stuck = this.element.hasClass(this.options.stickyClass);
                stickAfter = this._getOptionValue('stickAfter');

                if (offset && !stuck && offset < stickAfter) {
                    offset = 0;
                }

                if (this.element.outerHeight() > window.innerHeight) {
                    offset = 0;
                }

                this.element.toggleClass(this.options.stickyClass, offset > 0);

                if (offset > 0 && offset < this.maxOffset) {
                    this.element.css({
                        'position': 'fixed',
                        'left': this.postX,
                        'top': this.postT,
                        'width': this.widthE,
                        'transform': 'translate3d(0, 0, 0)'
                    });
                } else if (offset == this.maxOffset) {
                    this.element.css({
                        'position': 'relative',
                        'left': '',
                        'top': '',
                        'width': '',
                        'transform': 'translate3d(0, '+offset+'px, 0)'
                    })
                } else {
                    this.element.attr('style', '');
                }
            }
        },

        /**
         * Defines maximum offset value of the element.
         * @private
         */
        _calculateDimens: function () {
            var $parent         = this.element.parent(),
                parentHeight    = $parent.innerHeight(),
                height          = this.element.outerHeight(),
                maxScroll       = document.body.offsetHeight - window.innerHeight;

            if (this.options.container.length > 0) {
                maxScroll = $(this.options.container).innerHeight();
            }

            this.parentOffset   = $parent.offset().top;
            this.maxOffset      = maxScroll - this.parentOffset;

            if (this.maxOffset + height >= parentHeight) {
                this.maxOffset = parentHeight - height;
            }

            return this;
        },

        /**
         * Facade method that palces sticky element where it should be.
         */
        reset: function () {
            this.widthE = this.element.innerWidth()+'px';
            this.postX = this.element.offset().left+'px';
            this.postT = this.options.spacingTop+'px';
            this._calculateDimens()
                ._stick();
        }
    });

    return $.mage.sticky;
});
