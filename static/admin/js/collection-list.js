/**
 * Created by apple on 16/1/18.
 */

(function ($) {
    "use strict";
    //Constructor
    $.CollectionList = function (options, container) {
        this.settings = $.extend(true, {}, $.CollectionList.defaults, options);
        this.container = container;
        this.init();
    };

    $.extend($.CollectionList, {
        defaults: {
            "listUrl": "/collection/my_list",
            "itemTpl": $.templates("#item-tpl"),
            "userid": 0,
            "pageLimit": 20,
            "pageInitOffset": 0,
            "drawCallback": false
        },
        data: {},
        "prototype": {
            "cache": {},
            init: function () {
                var self = this;
                //this.fetchDataHandler(this.container,0,this.settings.pageLimit);
                $.post(this.settings.listUrl, {
                    "userid": this.settings.userid,
                    "of": 0,
                    "lm": this.settings.pageLimit,
                    "rt": "json"
                }, function (data) {
                    if (data.res == 0) {
                        self.setData (data);
                        self.setCache(data, 0);
                        if ($.type(self.pagination) == 'undefined') {
                            self.pagination = new Pagination(self.container, {
                                "total": data.total,
                                "limit": self.settings.pageLimit,
                                "gotoCallback": self.fetchDataHandler
                            })
                        }

                    } else {
                        showErrorMessage(data.hint);
                    }
                }, 'json');

            },
            //如何拿到cl的数据,从pagination过来
            fetchDataHandler: function (container, offset, limit) {
                var self = $.data(container, 'collection-list');
                if (self.getCache(offset)) {
                    self.setData(self.getCache(offset));
                    return;
                }

                $.post(self.settings.listUrl, {
                    "userid": self.settings.userid,
                    "of": offset,
                    "lm": limit,
                    "rt": "json"
                }, function (data) {
                    if (data.res == 0) {
                        self.setData (data);
                        self.setCache(data, offset);
                        //if($.type(self.pagination) == 'undefined'){
                        //    self.pagination = new Pagination(self.container, {
                        //        "total": data.total,
                        //        "limit": self.settings.pageLimit,
                        //        "gotoCallback": self.fetchDataHandler
                        //    })
                        //}

                    } else {
                        showErrorMessage(data.hint);
                    }
                }, 'json');

            },
            setCache: function (data, offset) {
                this.cache['off-' + offset] = data;
                //console.log(this.cache);
            },
            getCache: function (offset) {
                return this.cache['off-' + offset];
            },
            setData: function (data) {
                this.data = data;
                this.refreshView();
            },
            removeFromCache: function (data_ids) {
                var _offset = this.pagination.getOffset();
                delete(this.cache['off-' + _offset]);
                /*
                var _data = this.getCache(_offset);
                var _res = $.grep(_data.list, function (e, i) {
                    return $.inArray(e.id, data_ids) == -1;
                });
                _data.list = _res;
                this.setCache(_data,_offset);
                */
                //console.log(this.getCache(_offset));
            },
            refreshView: function () {
                this.container.html(this.settings.itemTpl.render(this.data.list));
                $('[data-toggle="tooltip"]').tooltip();
                this.settings.drawCallback();
                //for (var i = 0; i < this.data.list.length; i++) {
                //    this.container.loadTemplate(this.settings.itemTpl,
                //        this.data.list[i]);
                //}

            },

        }

    });


    //extend methods
    $.extend($.fn, {
            // http://jqueryvalidation.org/validate/
            collectionList: function (options) {
                // if nothing is selected, return nothing; can't chain anyway
                if (!this.length) {
                    if (options && options.debug && window.console) {
                        console.warn("Nothing selected, can't validate, returning nothing.");
                    }
                    return;
                }

                // check if a validator for this form was already created
                var cl = $.data(this, "collection-list");
                if (cl) {
                    return cl;
                }

                cl = new $.CollectionList(options, this);
                $.data(this, "collection-list", cl);

                return cl;
            },
        }
    );

    function Pagination(container, options) {
        this.container = container;
        var $self = this;
        var defaults = {
            offset: 0,
            limit: 20,
            total: 0,
            gotoCallback: function () {
            },

        };
        this.options = $.extend({}, defaults, options);

        this.getOffset = function () {
            return this.options.offset;
        }

        this.hasPrev = function () {
            if (this.options.offset >= this.options.limit) {
                return true;
            }
            return false;
        };
        this.hasNext = function () {
            if ((this.options.offset + this.options.limit) < this.options.total) {
                return true;
            }
            return false;
        };
        this.goto = function () {
            var pn = $(this).attr('data-pn');
            pn = Math.floor(pn);
            var newOffset = (pn - 1) * $self.options.limit;
            if (newOffset == $self.options.offset) {
                //当前页
                return false;
            }
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $self.options.offset = newOffset;
            $self.options.gotoCallback($self.container, $self.options.offset, $self.options.limit);
            return false;
        };
        //this.prev = function () {
        //    var pn = (this.options.offset / this.options.limit );
        //    if (pn <= 0) {
        //        return false;
        //    }
        //    this.goto(pn);
        //};
        //this.next = function () {
        //    var pn = (this.options.offset / this.options.limit + 2);   //pn ,1,2,3
        //    if (pn <= 0) {
        //        return false;
        //    }
        //    this.goto(pn);
        //};
        this.pageLink = function () {

            var _html = [''];
            if (this.options.total > this.options.limit) {
                var num = Math.ceil(this.options.total / this.options.limit);
                _html.push('<div class="text-center"><ul class="pagination">');
                //var currentPageNum = this.options.offset / this.options.limit + 1;
                for (var i = 1; i <= num; i++) {
                    var _active = (i == 1) ? 'active' : '';
                    _html.push('<li class="pn-goto ' + _active + '" data-pn="' + i + '"><a href="#">' + i + '</a></li>');
                }
                _html.push('</ul></div>');
            }
            //return _html.join('');
            this.container.after(_html.join(''));
        };

        this.bindListeners = function () {
            this.container.parent().on('click', 'li.pn-goto', this.goto);
        };

        this.pageLink();
        this.bindListeners();

    }

})(jQuery);
