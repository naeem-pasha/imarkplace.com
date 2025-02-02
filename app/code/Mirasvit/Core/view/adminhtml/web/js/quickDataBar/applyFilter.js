define([
    'uiComponent',
    'ko',
    'underscore',
    'jquery',
    'uiRegistry'
], function (Component, ko, _, $, registry) {
    'use strict';

    return Component.extend({
        initObservable: function () {
            this._super();

            this.loading = ko.observable(this.loading);
            this.data    = ko.observable(this.data);

            return this;
        },
        
        handleClick: function (filter, providerName, filterName) {
            if (!providerName || !filterName) {
                return false;
            }
            
            var filterBlock = registry.get(providerName);
            var filterElem  = registry.get(providerName + '.' + filterName);
    
    
            if (typeof filterBlock == 'undefined' || typeof filterElem == 'undefined') {
                return false;
            }
            
            filterBlock.clear();
            filterElem.value(filter);
            filterBlock.apply();
            
            return false;
        }
    });
});
