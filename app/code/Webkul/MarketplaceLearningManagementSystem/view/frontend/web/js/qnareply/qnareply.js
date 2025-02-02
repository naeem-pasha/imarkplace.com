/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define(
    [
        'jquery', 
        'uiComponent', 
        'ko',
        'mage/translate',
        'mage/url',
        'mage/storage',
    ], 
    function ($, Component, ko, $t, urlBuilder, storage) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Webkul_MarketplaceLearningManagementSystem/qnareply',
        },
        /**
         * initialize
         */
        initialize: function(config) {
            let self = this;
            this.recordData = ko.observableArray([]);
            this.replyData = ko.observableArray([]);
            this.questionData = ko.observableArray([]);

            this.courseId = config.courseId;
            this.contentId = config.contentId;
            this.baseUrl = config.baseUrl;
            this.currentUserSortName = config.currentUserSortName;
            this.msgIcon = config.msgIcon;
            this.updateQaRecord();
            this._super();
        },
        /**
         * Get Qa Record
         */
        getQaRecord: function() {
            return this.recordData();
        },
        /**
         * Update Qa Record
         */
        updateQaRecord: function () {
            let self = this;
            let url = this.baseUrl + "mplms/qarecord/index" + "?cid=" + this.courseId;
            $('body').loader('show');
            storage.get(
                url,
            ).fail(
                function (response) {
                    $('body').loader('hide');
                    alert('error during get qa-record data');
                }
            ).done(
                function (response) {
                    $('body').loader('hide');
                    self.recordData(response);
                    self.viewRecord();
                }
            );
        },
        /**
         * Update Qa Reply Data
         */
        updateQaReply: function (qaId) {
            let self = this;
            let url = this.baseUrl + "mplms/qareply/index" + "?qaid=" + qaId;
            if(qaId != null) {
                $('body').loader('show');
                storage.get(
                    url,
                ).fail(
                    function (response) {
                        $('body').loader('hide');
                        alert('error during get qa-record data');
                    }
                ).done(
                    function (response) {
                        $('body').loader('hide');
                        self.replyData(response);
                    }
                );
            }
        },
        /**
         * Get Qa Reply Data
         */
        getQaReply: function() {
            return this.replyData();
        },
        /**
         * Get Question Data
         */
        getQuestionData: function() {
            return this.questionData();
        },
        /**
         * View Reply 
         */
        viewReply: function(data) {
                this.questionData([data]);
                this.updateQaReply(data.entity_id);
                $('.qa-record').hide();
                $('.qa-reply').show();
                $('.qa-form').hide();
        },
        /**
         * View Record
         */
        viewRecord: function() {
            $('.qa-record').show();
            $('.qa-reply').hide();
            $('.qa-form').hide();

        },
        /**
         * View Form
         */
        viewForm: function() {
            $('.qa-record').hide();
            $('.qa-reply').hide();
            $('.qa-form').show();
        },
        /**
         * Get Msg Icon Url
         */
        getMsgIconUrl: function() {
            return this.msgIcon;
        },
        /**
         * Submit Record Data
         */
        submitRecord: function() {
            let qaTitle = $('#question-title').val();
            let qaDetail = $('#question-detail').val();
            let self = this;
            let url = this.baseUrl + "mplms/qarecord/save";
            if(qaTitle != null) {
                $.ajax({
                    url : url,
                    type: 'POST',
                    dataType: 'json',
                    data : {
                        title: qaTitle, 
                        detail: qaDetail,
                        courseId: this.courseId,
                        contentId: this.contentId
                    },
                    complete: function (response) {
                        $('body').loader('hide');
                        self.updateQaRecord(this.courseId);
                    },
                    error: function (response) {
                        $('body').loader('hide');
                        alert('error during save qa-record data');
                    }
                });
            }

        },
        /**
         * Submit Reply Data
         */
        submitReply: function() {
            let qaThread = $('#reply-detail').val();
            let qaId = this.questionData()[0].entity_id;
            let self = this;
            let url = this.baseUrl + "mplms/qareply/save";
            if(qaThread != null) {
                $.ajax({
                    url : url,
                    type: 'POST',
                    dataType: 'json',
                    data : {
                        qaThread: qaThread, 
                        qaId: qaId
                    },
                    complete: function (response) {
                        $('body').loader('hide');
                        self.updateQaReply(qaId);
                        $('#reply-detail').val('')
                    },
                    error: function (response) {
                        $('body').loader('hide');
                        alert('error during save qa-record data');
                    }
                });
            }
        },
        /**
         * Get CurrentUser Sort Name
         */
        getCurrentUserSortName: function () {
            return this.currentUserSortName;
        }
    });
});