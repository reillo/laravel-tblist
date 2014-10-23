/**
 * Laravel Tblist
 * http://github.com/nerweb93/laravel-tblist
 *
 * jQuery Helper
 */
jQuery(function ($) {

    // Helpers
    'use strict';

    function isDefined(data) {
        return typeof data !== 'undefined';
    }

    function isObject(data) {
        return typeof data === 'object';
    }

    function isString(data) {
        return typeof data === 'string';
    }

    var tblist = function () {

        var ajaxSubmitEnabledName = 'ajax-submit-enabled';

        this.options = {
            start: function () {
                return false;
            },
            end: function () {
                return false;
            },
            onSelect: function () {
                return false;
            },

            table: ".table-list",
            perPage: ".per-page",
            pagination: ".pagination",
            paginationInfo: ".pagination-info",
            ajaxSubmitEnabled: true
        };

        this.init = function ($list, options) {
            var _self = this;

            _self.$list = $list;
            _self.options = jQuery.extend({}, _self.options, options);

            // set object element
            _self.$perPage = _self.$list.find(_self.options.perPage);

            _self.params = [];

            // Initiate selectors
            _self._perPage();
            _self._pagination();
            _self._sorting();
            _self._cbSelect();
            _self._cbSelectAll();
            _self._formSubmit();
        };

        this._getDataParam = function () {
            var _self = this,
                strParam = '';

            if (_self.isAjaxSubmitEnabled()) {
                strParam = _self.$list.serialize();
            }

            for (var i in _self.params) {
                strParam += '&' + i + '=' + encodeURIComponent(_self.params[i]);
            }

            return strParam;
        };


        this._perPage = function () {
            var _self = this;

            _self.$perPage.change(function () {
                // override data params inputs from the form content
                _self.addParam('per_page', $(this).val());

                _self.refresh();
            });
        };

        this._pagination = function () {
            var _self = this,
                pagination = _self.options.pagination + " a";

            _self.$list.on('click', pagination, function (e) {
                e.preventDefault();

                var $this = $(this),
                    $parentLi = $this.closest('li');
                if ($parentLi.hasClass('disabled')) {
                    return false;
                }

                // Check if has attr attribute, then if found. Prevent from submitting.
                if (!isDefined($this.attr('disabled'))) {

                    // override data params inputs from the form content
                    _self._setActionUrl($this.attr('href'));

                    _self.refresh();
                }
            });
        };

        this._cbSelectAll = function () {
            var _self = this;

            _self.$list.on('change', 'input[type=checkbox].cb-select-all', function (e) {
                if ($(this).is(':checked')) {
                    _self.selectAllCb();
                } else {
                    _self.removeAllCb();
                }
            });
        };

        this._cbSelect = function () {
            var _self = this;

            _self.$list.on('change', 'input[type=checkbox].cb-select', function (e) {
                var data = {
                    count: _self.count(),
                    newselected: $(this).val(),
                    selected: _self.getCb()
                };

                _self.options.onSelect(data, _self.$list);
            });
        };

        this._sorting = function () {
            var _self = this,
                sortingAnchor = _self.options.table + ' .sorting';

            _self.$list.on('click', sortingAnchor, function (e) {
                var $this = $(this),
                    orderBy = $this.data('column-name'),
                    orderMethod = $this.hasClass('sorting_desc') ? 'desc' : 'asc';

                _self.addParam('column_orders[' + orderBy + ']', orderMethod);

                e.preventDefault();

                if (e.ctrlKey) {
                    $this.siblings().each(function (index) {
                        if ($(this).hasClass('sorting_desc') || $(this).hasClass('sorting_asc')) {
                            var orderBy = $(this).data('column-name'),
                                orderMethod = $(this).hasClass('sorting_desc') ? 'asc' : 'desc';

                            _self.addParam('column_orders[' + orderBy + ']', orderMethod);

                            _self.addParam('single_order', false);
                        }
                    });
                }
                else {
                    _self.addParam('single_order', orderBy);
                }

                _self._setActionUrl($this.children('a').attr('href'));

                _self.refresh();
            });
        };

        this._formSubmit = function () {
            var _self = this;

            if (_self.isAjaxSubmitEnabled()) {
                _self.$list.on('submit', function (e) {
                    e.preventDefault();

                    _self.refresh();
                });
            }
        };

        this.isAjaxSubmitEnabled = function () {
            var _self = this;

            // Form enable data-parameter always take presidense
            if (_self.$list.data(ajaxSubmitEnabledName)
                && _self.$list.data(ajaxSubmitEnabledName) == true) {
                return true;
            }
            else {
                if (_self.options.ajaxSubmitEnabled) {
                    return true;
                }
            }

            return false;
        };

        this.addParam = function (name, value) {
            var _self = this;

            _self.params[name] = value;
        };

        this._setActionUrl = function (action) {
            var _self = this;

            _self.$list.attr('action', action);
        };

        /*!
         * Get Action url from tblist
         *
         * return String
         */
        this._getActionUrl = function () {
            var _self = this;

            return _self.$list.attr('action');
        };

        // Public method and API
        this.refresh = function () {
            var _self = this,
                $table = _self.$list.find(_self.options.table),
                $pagination = _self.$list.find(_self.options.pagination),
                $paginationInfo = _self.$list.find(_self.options.paginationInfo),
                parameter = _self._getDataParam();

            // reset everyting before we start, so call again on end
            _self._onEnd();

            // if has previous request that still on process then abort it.
            !_self.xhr || _self.xhr.abort();

            // Check form data type
            _self.xhr = $.ajax({
                type: _self.$list.attr('method'),
                url: _self._getActionUrl(),
                data: parameter,
                dataType: 'json',
                beforeSend: function () {
                    _self._onStart();
                    _self.options.start(parameter, _self.$list);

                },
                success: function (data) {
                    // Status Success Message
                    $table.replaceWith(data.tableData);

                    if (isDefined(data.pagination)) {
                        $pagination.replaceWith(data.pagination);
                    }

                    if (isDefined(data.paginationInfo)) {
                        $paginationInfo.replaceWith(data.paginationInfo);
                    }
                }
            }).done(function (reponse) {
                _self._onEnd();
                _self.options.end(reponse, _self.$list);

                _self.xhr = false;
            });
        };

        this._onStart = function () {
            var _self = this,
                $table = _self.$list.find(_self.options.table),
                $tbody = $table.children('tbody'),
                position = $tbody.position(),
                top = position.top,
                left = position.left,
                height = $tbody.height(),
                width = $tbody.width();

            var $overlay = $('<div class="table-overlay"><span class="table-loader"></span></div>');
            $overlay.insertAfter($tbody);

            $overlay.css({
                'position': 'absolute',
                'background': 'rgba(255, 255, 255, 0.74)',
                'top': top + 'px',
                'left': left + 'px',
                'width': width + 'px',
                'height': height + 'px'
            });

            $('.table-loader').css({
                'margin-top': (height / 3) + 'px',
                'margin-left': 'auto',
                'margin-right': 'auto'
            });
        };

        this._onEnd = function () {
            var _self = this;

            _self.$list.find('.table-overlay').remove();
            _self.params = [];
        };

        this.count = function () {
            var _self = this;

            var cbids = _self.getCb();

            return cbids.length;
        };

        this.getCb = function () {
            var _self = this;

            var cbids = _self.$list.find('input[type=checkbox].cb-select:checked').map(function () {
                return this.value;
            }).get();

            return cbids;
        };

        this.selectCb = function (toSelect) {
            var _self = this;

            _self.$list.find('input[type=checkbox].cb-select-id-' + toSelect).prop('checked', 'checked');

            return toSelect;
        };

        this.selectAllCb = function () {
            var _self = this;

            _self.$list.find('input[type=checkbox].cb-select,' +
                    'input[type=checkbox].cb-select-all')
                .prop('checked', 'checked');

        };

        this.removeCb = function (toRemove) {
            var _self = this;

            _self.$list.find('input[type=checkbox].cb-select-id-' + toRemove)
                .prop('checked', false);
        };

        this.removeAllCb = function () {
            var _self = this;

            _self.$list.find('input[type=checkbox].cb-select,' +
                    'input[type=checkbox].cb-select-all')
                .prop('checked', false);
        };

    };


    $.fn.tblist = function (option, val) {
        var $this = $(this);
        var $global_tblist = new tblist;

        // Default would be object
        option = isDefined(option) ? option : [];

        if (isString(option)) {
            // If an option
            $global_tblist.$list = $this.first();

            if (isDefined($global_tblist[option])) {
                return $global_tblist[option](val);
            }
        } else if (isObject(option)) {
            // if an object, then initialize tblist
            $this.each(function () {
                var $tblist = new tblist;
                $tblist.init($(this), option);
            });

            return;
        }

        $.error('Method ' + option + ' does not exist on tblist.jquery.js');
    };


});