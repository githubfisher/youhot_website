/**
 * Created by apple on 15/12/15.
 */

'use strict';


var CONFIG = {

    product: {
        "get_url": "/product/preview",
        "create_url": "/admin/product/create",
        "save_url": "/admin/product/save",
        "delete_url": "/admin/product/delete",
        "publish_url": "/admin/product/publish",
        "unpublish_url": "/admin/product/unpublish",
        "audit_url": "/admin/product/to_audit",
        "preview_url": "/product/preview",
        "admin_edit_url": "/admin/product/"
        , 'my_list': '/product/my_list'
        , "admin_product_list_url": "/admin/product/list"
        , 'update_color_url': '/admin/product/color/save',
	'filter_product': '/product/filterProduct'
	,'search_product': '/product/filterProductNew',

    },
    order: {
        "get_url": "/order/preview",
        "create_url": "/order/create",
        "update_url": "/order/update",
        "seller_list_url": "/order/get_list/seller",
        "preview_url": "/order/preview",
        "admin_edit_url": "/admin/order/",
        "admin_index_url": "/admin/order"
        , 'my_list': '/order/my_list'
        , "admin_product_list_url": "/order/product/list"
    },
    category: {
        "get_list_url": "/category/orderedlist",
        "update_url": "/admin/category/update",
        "delete_url": "/admin/category/delete",
        "add_url": "/admin/category/create",
    },
    banner: {
        "get_list_url": "/banner/get_list",
        "update_url": "/admin/banner/update",
        "delete_url": "/admin/banner/delete",
        "add_url": "/admin/banner/create",
    },
    user: {
        "logout_url": "/user/logout",
        "save_url": "/user/supply_detail",
        "list_url": "/admin/user/get_list"
        , "admin_index_url": "/admin/user"
        , "admin_edit_url": "/admin/user/"
        , "admin_setrole_url": "/user/set_role"
        , "admin_delete_url": "/admin/user/delete"
        , "face_upload_url": "/aliyun/upload/avatar"
    },
    applicant: {
        "approve_url": "/admin/applicant/approve",
        "list_url": "/admin/applicant/get_list",
        "save_url": "/admin/applicant/save"

    },
    collection: {
        "save_url": "/collection/save",
        "list_url": "/collection/list",
        "preview_url": "/collection/detail",
        "admin_list_url": "/admin/collection"
        , "admin_edit_url_prefix": "/admin/collection/"
        , "admin_index_url": "/admin/collection"
        , "create_url": "/admin/collection/create",
    },
    tag: {
        "get_list_url": "/tag/get_list",
        "update_url": "/product/tags/save"
    },
    color: {
        "add_url": "/admin/product/color/add",
    },
    size: {

        "update_url": "/product/size/save"
    },
    album: {
        "add_url": "/product/album/add",
        "delete_url": "/product/album/delete",
        "update_url": "/product/album/update",
        "upload_url": "/aliyun/upload"
    },
    datas: {
	"get_data": "/data/get_data"
    },
    hint: {
        'get_product_info_error': '获取商品信息出错了',
        'create_product_error': '创建商品出错了',
        'ajax_error': '操作出错了'

    },
    ALBUM_TYPE: {
        "IMAGE": 1
        , "VIDEO": 2
    },
     EDITABLE_OPTIONS : {
        'submit': '确定',
        'cancel': '取消',
        'indicator': '提交中，请稍候...',
        'tooltip': '点击可进行编辑',
        'placeholder': ' ',
        'submitdata': {},
        'event': 'click',
        'onblur': "ignore",
        'width':'100%',
        'height':'32px',
        ajaxoptions: {
            dataType: 'json'
        },
//            'data': function (value, config) {
//                console.log(value);
//                var v = $('<p></p>').html(value).text();
//                return v;
//            },
        'onsubmit': function (config, element) {
//                console.log($(element).parent());
//                var c = oTable.cell($(element));
//
//                var idx = c.index().column;
//                var keyname = oTable.column( idx ).dataSrc() ;
//
//                config.submitdata['cat_id'] = oTable.row($(element).parent()).id();
//                config.submitdata[keyname] = $(':input:first', this).val();
////                config.submitdata['userid'] = $(':input:first', this).val();
//                $(element).data('v', $(':input:first', this).val());
                console.log("onsbumit");
            //增加一个扩展
            if (config.submitHandle && typeof(config.submitHandle) == 'function') {
                console.log("submitHandle");
                config.submitHandle.apply(this, [config, element]);
            }
        },

        callback: function (value, settings) {
            var v = $('<p></p>').text($(this).data('v')).html().replace(/\n/g, '<br>\n');

            $(this).html(v);

            if (value.res == 0) {
                showSuccess();
            } else {
                showErrorMessage(value.hint);
                $(this).html(this.revert);
            }
        }

    }

};

var transform = function (data) {
    return $.param(data);
}
//default rt param
$.ajaxSetup({
    data: {
        rt: "json"
    }
});

//helper
function showErrorMessage(msg) {
    Messenger({
        extraClasses: 'messenger-fixed messenger-on-right messenger-on-top',
        theme: 'flat'
    }).post({
        message: msg,
        type: 'error',
        showCloseButton: true,
        hideOnNavigate:true,
        hideAfter: 2,
        id: "one-message"

    });
}

function progressMessage(msg) {
    var i = 0;
    Messenger({
        extraClasses: 'messenger-fixed messenger-on-right messenger-on-top',
        theme: 'flat'
    }).post(
        {
            message: "<img src='/static/images/loading.gif' />" + msg,
            id: "one-message",
            hideAfter: 100000
        }
    );
}

function showSuccess(msg) {
    if (typeof msg == 'undefined') {
        msg = '操作成功';
    }
    Messenger({
        extraClasses: 'messenger-fixed messenger-on-right messenger-on-top',
        theme: 'flat'
    }).post(
        {
            message: msg,
            id: "one-message",
            hideAfter: 2,
            hideOnNavigate:true,
            showCloseButton: true
        }
    );
}
$( document ).ajaxError(function() {
    //$( ".log" ).text( "Triggered ajaxError handler." );
    showErrorMessage("Server出现了点问题,请稍后再试");
});
//获取url参数值
function get_param_value(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}


if ($.isFunction($.fn.dataTable)) {
    //
// Pipelining function for DataTables. To be used to the `ajax` option of DataTables
//
    $.fn.dataTable.pipeline = function (opts) {
        // Configuration options
        var conf = $.extend({
            pages: 5,     // number of pages to cache
            url: '',      // script url
            data: null,   // function or object with parameters to send to the server
                          // matching how `ajax.data` works in DataTables
            method: 'GET' // Ajax HTTP method
        }, opts);

        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;

        return function (request, drawCallback, settings) {
            var ajax = false;
            var requestStart = request.start;
            var drawStart = request.start;
            var requestLength = request.length;
            var requestEnd = requestStart + requestLength;

            if (settings.clearCache) {
                // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            }
            else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
                // outside cached data - need to make a request
                ajax = true;
            }
            else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
                JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
                JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
            ) {
                // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend(true, {}, request);

            if (ajax) {
                // Need data from the server
                if (requestStart < cacheLower) {
                    requestStart = requestStart - (requestLength * (conf.pages - 1));

                    if (requestStart < 0) {
                        requestStart = 0;
                    }
                }

                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * conf.pages);

                request.start = requestStart;
                request.length = requestLength * conf.pages;

                // Provide the same `data` options as DataTables.
                if ($.isFunction (conf.data)) {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = conf.data(request);
                    if (d) {
                        $.extend(request, d);
                    }
                }
                else if ($.isPlainObject(conf.data)) {
                    // As an object, the data given extends the default
                    $.extend(request, conf.data);
                }

                settings.jqXHR = $.ajax({
                    "type": conf.method,
                    "url": conf.url,
                    "data": request,
                    "dataType": "json",
                    "cache": false,
                    "dataFilter": function (data) {
//                    var re_data = {};
                        data = jQuery.parseJSON(data);
                        if (data.res == 0) {
                            data.recordsTotal = data.total;
                            data.recordsFiltered = data.total;
                            data.data = data.list;
                            delete data.list;
                            delete data.total;
                        } else {
                            data.error = data.hint;
                        }
//                    console.log(data);
                        return JSON.stringify(data);
                    },
                    "success": function (json) {
                        cacheLastJson = $.extend(true, {}, json);
                        if (json.res == 0) {
                            if (cacheLower != drawStart) {
                                json.data.splice(0, drawStart - cacheLower);
                            }
                            console.log(json);
                            json.data.splice(requestLength, json.data.length);
                        } else {
                            json.data = [];
                        }

                        drawCallback(json);
                    }
                });
            }
            else {
                var json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                json.data.splice(requestLength, json.data.length);

                drawCallback(json);
            }
        }
    };

// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
    $.fn.dataTable.Api.register('clearPipeline()', function () {
        return this.iterator('table', function (settings) {
            settings.clearCache = true;
        });
    });
}

jQuery(function ($) {

    'use strict';

    var ULTRA_SETTINGS = window.ULTRA_SETTINGS || {};


    /*--------------------------------------*/


    /*--------------------------------
     Tooltips & Popovers
     --------------------------------*/
    ULTRA_SETTINGS.tooltipsPopovers = function () {

        $('[rel="tooltip"]').each(function () {
            var animate = $(this).attr("data-animate");
            var colorclass = $(this).attr("data-color-class");
            $(this).tooltip({
                template: '<div class="tooltip ' + animate + ' ' + colorclass + '"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
            });
        });

        $('[rel="popover"]').each(function () {
            var animate = $(this).attr("data-animate");
            var colorclass = $(this).attr("data-color-class");
            $(this).popover({
                template: '<div class="popover ' + animate + ' ' + colorclass + '"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });
        });

    };


    /*------------------------------------*/

    // tagsinput
    if ($.isFunction($.fn.tagsinput)) {

        // categorize tags input
        var i = -1,
            colors = ['primary', 'info', 'warning', 'success'];

        //colors = shuffleArray(colors);

        $("#field-color").tagsinput({
            tagClass: function () {
                i++;
                return "label label-" + colors[i % colors.length];
            },
            confirmKeys: [13, 44, 65292, 59, 65307]
        });


        $("#field-size").tagsinput({
            tagClass: function () {
                i++;
                return "label label-" + colors[i % colors.length];
            }
            , confirmKeys: [13, 44, 65292, 59, 65307]
        });


    }
    var PRODUCT = {}
    PRODUCT.save = function () {

        //form.$setSubmitted();
        //console.info(form.username.$error);
        if ($.isFunction($.fn.validate)) {
            var $valid = $("#product_form").valid();
            if (!$valid) {
                $product_form_validator.focusInvalid();
                return false;
            }
        }
        //console.log(this.product);
        //console.log($scope.product);

        $.post(CONFIG.product.save_url, $("#product_form").serialize(), function (data) {

            if (data && data.res == 0) {
                showSuccess('保存成功');
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');

    }
    PRODUCT.save_album = function (event) {
        var _url = CONFIG.album.add_url;
        var _inputs = $(this).parent('.form-group').find('input');
        var _data=_inputs.serialize(), _raw_form_input = _inputs.serializeArray();
        var _form_input={};
        $.each(_raw_form_input, function(i, v) {
            _form_input[v.name] = v.value;
        })
        console.log(_form_input);
        //Is video and is replace
        var _form_album_id = _form_input.album_id;
        if (typeof _form_album_id != 'undefined' && _form_album_id > 0) {
            _url = CONFIG.album.update_url;
            var album_id = _form_album_id;
            _data = {
                'up_data': [{
                    "album_id": album_id,
                    'product_id': _form_input.product_id,
                    "content": _form_input.content,
                }]
            }

            if(_form_input.content == ''){
                console.log('delete album');
                _url = CONFIG.album.delete_url;   //Video will be deleted if conent is null.
                _data = {
                    "album_id": album_id,
                }
            }
        }

        $.post(_url, _data, function (data) {

            if (data && data.res == 0) {
                showSuccess('保存成功');
                //location.href = CONFIG.product.admin_product_list_url;
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');

        return false;

    }


    //product edit
    $('#ossfile').on('mouseenter', '.preview-img-ctn', function () {
        console.log('mouseenter');
        $(this).append($("<span class='btn album-del glyphicon glyphicon-remove-circle' title='删除'></span>"));
    });
    $('#ossfile').on('mouseleave', '.preview-img-ctn', function () {
        $(this).find("span:last").remove();
    });
    $('#ossfile').on('click', '.album-del', function () {
        var _ctner = $(this).parent();
        var params = {
            'album_id': _ctner.data('item-id'),
            'product_id': _ctner.data('pid'),
            'rt': 'json'
        };
        $.post(CONFIG.album.delete_url, params, function (data) {
            if (data.res == '0') {
                showSuccess('删除成功');
                _ctner.remove();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
        return false;
    });

    //$('.preview-img-ctn').hover(
    //    function() {
    //        $( this ).append( $( "<span class='glyphicon glyphicon-remove album-del'>删</span>" ) );
    //    }, function() {
    //        $( this ).find( "span:last" ).remove();
    //    }
    //);
    //end of product edit

    /*-----------------------*/
//form validation
    if ($.isFunction($.fn.validate)) {
        jQuery.validator.addMethod(
            'alpha_dash',
            function (value, element) {
                return this.optional(element)
                    || /^[A-Za-z0-9_-]+$/ig.test(value);
            },
            "密码必须由数字，英文及_-组成"
        );
        jQuery.validator.addMethod(
            'max_byte_length',
            function (value, element, params) {
                //console.log(params);
                //console.log((value.replace(/[^\u0000-\u00ff]/g,"aaa").length ));
                return this.optional(element)
                    || (value.replace(/[^\u0000-\u00ff]/g, "aaa").length < params);
            },
            "请输入一个长度最多是 {0} 的字符串"
        );
        jQuery.validator.addMethod(
            'email_phone',
            function (value, element) {
                var isPhone = (this.optional(element) || /^\d+$/.test(value)) && this.getLength($.trim(value), element) <= 12 && this.getLength($.trim(value), element) >= 11 ;
                var isEmail = this.optional(element) || /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(value);

                return isPhone || isEmail;
            },
            "请输入手机号或者email"
        );
        $('#user-edit-form').validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                nickname: {
                    maxlength: 120,
                },
                //age: {
                //    number: true,
                //},
                password: {
                    alpha_dash: true,
                }
            },
            submitHandler: function (form) {
                //alert('ss');
                //update role
                var role;
                $('input[name=role-check]:checked', form).each(function () {
                    role = role | (~~$(this).val())
                })
                //console.log(role);

                //update user info
                $.post(CONFIG.user.save_url + '?rt=json', $(form).serialize(), function (data) {
                    if (data && data.res == 0) {
                        showSuccess('保存成功');

                        if (role > 0) {

                            form.role.value = role;
                            $.post(CONFIG.user.admin_setrole_url, {
                                'userid': form.userid.value,
                                'role': role
                            }, function (data) {
                                if (data && data.res == 0) {
                                    showSuccess('权限设置成功');
                                    //location.href = CONFIG.user.admin_index_url;
                                } else {
                                    showErrorMessage(data.hint);
                                    return false;
                                }
                            }, 'json');
                        } else {
                            //location.href = CONFIG.user.admin_index_url;
                            //history.back();
                        }
                        //location.href = CONFIG.user.list_url;


                    } else {
                        showErrorMessage(data.hint);
                    }
                }, 'json');

                return false;
            }
        });
        $('#styl-form').validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                brand_name:{
                    required:true,
                    minlength:1,
                    maxlength:30,
                    alpha_dash:true

                },
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 20,
                    alpha_dash:true
                },
                telephone: {
                    required: true,
                    minlength: 1,
                    maxlength:16,
                    alpha_dash:true
                }
            },
            submitHandler: function (form) {
                //update user info
                $.post('/user/applicant/submit', $(form).serialize(), function (data) {
                    if (data && data.res == 0) {
                        location.href ='/static/refer.html';

                    } else {
//                        showErrorMessage(data.hint);
                        alert("账号或手机号验证失败")
                    }
                }, 'json');

                return false;
            }
        });
        $('#col-edit-form').validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                collection_id: {
                    required: true,
                },
            },
            submitHandler: function (form) {
                //update user info
                $.post(CONFIG.collection.save_url + '?rt=json', $(form).serialize(), function (data) {
                    if (data && data.res == 0) {
                        showSuccess('保存成功');

                        //location.href = CONFIG.collection.admin_list_url;

                    } else {
                        showErrorMessage(data.hint);
                    }
                }, 'json');

                return false;
            }
        });

        //var $album_form_validator = $('#album_form').validate({
        //    focusInvalid: false,
        //    ignore: "",
        //    rules: {
        //        content: {
        //            url: true,
        //            //required: true
        //        }
        //    },
        //    submitHandler: function (form) {
        //        //alert('ss');
        //        PRODUCT.save_album(form);
        //        return false;
        //    }
        //});

        $('#product_form').on('blur','input#album-video',PRODUCT.save_album);
        var $product_form_validator = $('#product_form').validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                title: {
                    required: true
                    , max_byte_length: 60
                },
                price: {
                    required: true,
                    number: true,

                },
                presale_price: {
                    required: true,
                    number: true,

                },
                presale_minimum: {
                    required: true,
                    digits: true,

                },
                presale_maximum: {
                    required: true,
                    digits: true,

                },
                presale_days: {
                    required: true,
                    digits: true,
                },
                production_days: {
                    required: true,
                    digits: true
                },
                size_ids: {
                    required: true,
                },
                color_ids: {
                    required: true,
                },


            },

            invalidHandler: function (event, validator) {
                //display error alert on form submit
            },

            errorPlacement: function (label, element) { // render error placement for each input type
                console.log(label);
                $('<span class="error"></span>').insertAfter(element).append(label)
                var parent = $(element).parent().parent('.form-group');
                parent.removeClass('has-success').addClass('has-error');
            },

            highlight: function (element) { // hightlight error inputs
                var parent = $(element).parent().parent('.form-group');
                parent.removeClass('has-success').addClass('has-error');
            },

            unhighlight: function (element) { // revert the change done by hightlight

            },

            success: function (label, element) {
                var parent = $(element).parent().parent('.form-group');
                parent.removeClass('has-error').addClass('has-success');
            },

            submitHandler: function (form) {
                //alert('ss');
                PRODUCT.save();
                return false;
            }
        });

        jQuery.extend(jQuery.validator.messages, {
            required: "必选字段",
            remote: "请修正该字段",
            email: "请输入正确格式的电子邮件",
            url: "请输入合法的网址",
            date: "请输入合法的日期",
            dateISO: "请输入合法的日期 (ISO).",
            number: "请输入合法的数字",
            digits: "只能输入整数",
            creditcard: "请输入合法的信用卡号",
            equalTo: "请再次输入相同的值",
            accept: "请输入拥有合法后缀名的字符串",
            maxlength: jQuery.validator.format("请输入一个长度最多是 {0} 的字符串"),
            minlength: jQuery.validator.format("请输入一个长度最少是 {0} 的字符串"),
            rangelength: jQuery.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),
            range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
            max: jQuery.validator.format("请输入一个最大为{0} 的值"),
            min: jQuery.validator.format("请输入一个最小为{0} 的值")
        });
    }

    $("#product_form").on('click', 'button[name=desc-add]', function (e) {
        var _li = '<li><input name="desc_title[]" value="" placeholder="特征"  class="col-sm-5"><span class="col-sm-1">-</span><input name="desc_content[]" value=""  class="col-sm-5"></li>';
        $('#desc-ctner').append(_li);
        return false;
    });
    $("#product_form").on('mouseenter', '#desc-ctner li', function (e) {
        $(this).append('<span class="glyphicon glyphicon-remove-circle desc-del col-sm-1"></span>')
        return false;
    });
    $("#product_form").on('mouseleave', '#desc-ctner li', function (e) {
        $('span:last', this).remove();
        return false;
    });
    $("#product_form").on('click', '.desc-del', function (e) {
        $(this).parent().remove();
        return false;
    });

    $("#product_form").on('click', '#color-add-btn', function (e) {
        $('#color-add-form').toggle();
        return false;
    });
    $("#product_form").on('click', '#color-submit', function (e) {

        var vals = $(this).parent().find('input').serialize();
        var $self = $(this);
        $.post(CONFIG.color.add_url, vals + '&rt=json', function (data) {
            if (data.res == 0) {
                showSuccess();
                var target = $self.parents('.biaoqian-small').prev();
                var _color_item = target.clone();
                //change value
                _color_item.find('input[name=color_ids]').val(data.color_id).attr('checked', 'true')
                _color_item.find('span').text(data.name);
                _color_item.find('img').attr('src', data.image);
                //end ;insert to dom
                _color_item.insertAfter(target).find('input[name=color_ids]').trigger('change');

                $self.parent('#color-add-form').toggle();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
        return false;
    });


    var a, b, c, d, e;
    e = function () {
        console.log(this);
        return this.find("input.cropit-image-input").click()
    };
    a = function () {
        var a;
        return a = this.cropit("export"),
            window.open(a)
    };
    d = function () {
        return this.find(".slider-wrapper").removeClass("disabled")
    };
    c = function () {
        return this.find(".slider-wrapper").addClass("disabled")
    };
    b = function (a) {
        return 1 === a.code ? (this.find(".error-msg").text("图片要求最小宽度: " + this.outerWidth() + "像素;最小高度: " + this.outerHeight() + "像素"), this.addClass("has-error"), window.setTimeout(function (a) {
            return function () {
                return a.removeClass("has-error")
            }
        }(this), 3e3)) : void 0
    };

    var album_submit = function () {
        // Move cropped image data to hidden input
        var imageData = this.cropit('export');
        var files = this.find('input[name=file]').val();
        //console.log(files);
        files = files.split('\\');
        var filename = "";
        if (files.length > 2) {
            filename = files[2];
        } else {
            //var datauri = this.cropit('imageSrc');
            var datatype = imageData.match(/^data\:image\/([^;]*)/)[1];
            filename = (new Date()).getTime() + '.' + datatype;
        }
        this.find('input[name=filename]').val(filename);

        //console.log($('.image-editor').cropit('imageSrc'));
        //console.log($('.image-editor').cropit('imageSize'));

        //return false;

        // Print HTTP request params
        if (typeof imageData == "undefined") {
            alert("请上传图片");
            return false;
        }
        var contentType = 'image/png';
        var va = imageData.split(',');
        this.find('input[name=file_content]').val(va[1]);

        //var formValue = $("#cover-image-form").serialize();
        var self = this;
        var _url = CONFIG.album.upload_url;
        if (self.attr('id') == 'face-cropper') {
            _url = CONFIG.user.face_upload_url;
        }
        $.ajax({
            method: "POST",
            url: _url,
            data: this.find("form").serialize(),
            dataType: 'json',
            success: function (data) {
                if (data && data.res == 0) {
                    //$scope.product = data;
                    showSuccess('上传成功');
                    //var preview_img = '<img src="' + data.file_url + '" class="img-thumbnail img-responsive">';

                    //$('.cropit-image-preview-container').html(preview_img);
                    var modal_id = self.attr('id');
                    switch (modal_id) {
                        case 'image-cropper':
                            $('#p-cover-image').attr('src', data.file_url + '@136h_1wh');
                            $('input[name=cover_image]').val(data.file_url);
                            update_product_cover(g_product_id, data.file_url);
                            $('#crop-image-modal').modal('hide');
                            break;
                        case 'album-image-cropper':
                            var position = $('#ossfile .preview-img-ctn').length;
                            submit_img_to_album(g_product_id, data.file_url, position);
                            var _html = '<div id="album-image" class="preview-img-ctn clearfix"><img ' +
                                'src="' + data.file_url + '@136h_1wh" ' +
                                'class="img-thumbnail img-responsive"></div>';
                            $('#ossfile').append(_html);
                            $('#crop-album-modal').modal('hide');
                            break;
                        case 'col-cover-image-cropper':
                            $('input[name=cover_image]').val(data.file_url).next('img').attr('src', data.file_url + '@200h_1wh');
                            if (self.attr('data-cid') > 0) {
                                save_collection_cover_image(self.attr('data-cid'), data.file_url);
                            }
                            $('#crop-image-modal').modal('hide');
                            break;
                        case 'col-item-image-cropper':
                            var _trigger = $('#crop-item-image-modal').data('originTrigger');
                            _trigger.find('input[name=content]').val(data.file_url).next('img').attr('src', data.file_url + '@200h_1wh');
                            _trigger.parents('form.col-item-form').trigger('submit');
                            $('#crop-item-image-modal').modal('hide');
                            break;
                        case 'face-cropper':
                            $("input[name=facepic]").val(data.file_url).next('img').attr('src', data.file_url + '@50h_1wh');
                            $('#crop-face-modal').modal('hide');
                            break;
                        case 'color-cropper':
                            $("input[name=image]").val(data.file_url).next('img').attr('src', data.file_url + '@40h_1wh');
                            $('#color-modal').modal('hide');
                            break;
                        case 'user-cover-cropper':
                            $("input[name=cover]").val(data.file_url).next('img').attr('src', data.file_url + '@200h_1wh');
                            $('#user-cover-modal').modal('hide');

                        default:
                            break;
                    }


                } else {
                    showErrorMessage(data.hint);
                }
            },
            beforeSend: function () {
                progressMessage("图片上传中");
            },
            complete: function () {

            }
        });
        return false;
    };
    var g = $('#image-cropper,#album-image-cropper,#col-cover-image-cropper,.image-big-cropper,#face-cropper,#color-cropper');
    g.each(function () {
        var f = $(this);
        f.cropit({
            imageBackground: true,
            imageBackgroundBorderWidth: 20,
            exportZoom: 1,
            onZoomEnabled: d.bind(f),
            onZoomDisabled: c.bind(f),
            onImageError: b.bind(f.find(".cropit-image-preview")),
            maxZoom: 2,
            smallImage: 'stretch',
            minZoom: "fit",
            initialZoom: "image",
            //onImageLoaded: croper_image_load_cb.bind(f),
            //onZoomChange: croper_image_load_cb.bind(f),
            //onFileChange:croper_file_ch_cb.bind(f)
        });

        f.on("click", ".select-image-btn", e.bind(f));
        f.on("click", ".auto-height-btn", croper_image_auto_height.bind(f));
        f.on("click", ".upload-btn", album_submit.bind(f));
    });

    function croper_image_auto_height() {
        if (this.attr('id') != 'col-item-image-cropper') {
            return;
        }
        var size = {'width': 650, "height": 680};
        var zoom = this.cropit('zoom');
        var img_size = this.cropit('imageSize');
        //var preview_size = this.cropit('previewSize');
        //console.log(img_size);
        //console.log(preview_size);
        if (img_size.height * zoom > 100 && img_size.height * zoom < size.height) {
            this.cropit('previewSize', {width: size.width, height: img_size.height * zoom});
        } else if (img_size.height * zoom < 100) {
            alert('图片高度太小了');
        } else {
            this.cropit('previewSize', size);
        }
        //else if (img_size.height * zoom < size.height) {
        //    this.cropit('previewSize', {width: size.width, height: img_size.height * zoom});
        //}
        return;
    }

    function croper_file_ch_cb() {
        var preview_size = this.cropit('previewSize');
        console.log(preview_size);
        return this.cropit('previewSize', preview_size);
    }

    function submit_img_to_album(product_id, url, position) {
        var data = {
            "product_id": product_id
            , "content": url
            , "type": CONFIG.ALBUM_TYPE.IMAGE
            , "position": position

        }
        $.post(CONFIG.album.add_url, data, function (res) {
            if (res.res != 0) {
                showErrorMessage(res.hint);
            }
        }, 'json');
    }

    function update_product_cover(product_id, url) {
        var data = {
            "product_id": product_id
            , "cover_image": url

        }
        $.post(CONFIG.product.save_url, data, function (res) {
            if (res.res != 0) {
                showErrorMessage(res.hint);
            }
        }, 'json');
    }

    function save_collection_cover_image(col_id, url) {
        var data = {
            "collection_id": col_id
            , "cover_image": url
            , 'rt': 'json'
        }
        $.post(CONFIG.collection.save_url, data, function (res) {
            if (res.res != 0) {
                showErrorMessage(res.hint);
            }
        }, 'json');
    }

    $('#sel-product-modal').on('hide.bs.modal', function (e) {
        var _target = $(this).data('data-item');
        // do something...
        console.log(_target);
        var $checked = $('#sel-product-modal .modal-body input[type=radio]:checked');
        if($checked.length >0) {
            var _c = $checked.next();
            var _src = _c.find('img').removeAttr('width').attr('src').replace('@50h', '@20h');
            _c.find('img').attr('src', _src);
            var _img = $('<div class="rp-content"></div>').append(_c);
            _target.find('input[name=product_id]').val($checked.val());
            _target.find('.rp-ctner').html(_img);
        }
    })
    $('#crop-item-image-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        $('#crop-item-image-modal').data('originTrigger', button);
        //$(this).find('.select-image-btn').trigger('click');

    })

    $('.modal').each(function (index, ele) {
        $(this).on('show.bs.modal', function (event) {
            $(this).find('.select-image-btn').trigger('click');

        })
    });


    $('.col-item-form').each(function (key, form) {
        var $self = $(form);
        $(form).validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                text: {
                    required: false,//true,
                },
            },
            submitHandler: function (form) {
                //update user info
                //console.log(form);
                $.post(form.action + '?rt=json', $(form).serialize(), function (data) {
                    if (data && data.res == 0) {
                        showSuccess('保存成功');
                        $self.find('.form-group').addClass('saved');
                        //location.href = CONFIG.collection.admin_list_url;

                    } else {
                        showErrorMessage(data.hint);
                    }
                }, 'json');

                return false;
            }
        })
    });

    $('li.item-placeholder').on('click', '.item-add-btn', function (e) {
        var _parent_li = $(this).parents('li.item-placeholder');
        var _li = $("#item-add-tpl").clone(true);
        _parent_li.before(_li.html());

        //console.log(_li);
        //console.log(_parent_li);
        //_li.find('form').attr('action','/collection/album/add');
        //_li.find('textarea').html('');
        //_li.find('input[name=product_id]').val('');
        //_li.find('input[name=content]').val('');
        //_li.find('img.col-cover-image').attr('src','');
        //_li.find('img.col-relate-product').attr('src','');

        $('form.col-itemadd-form').each(function (key, form) {
            var $self = $(this);
            $(form).validate({
                focusInvalid: false,
                ignore: "",
                rules: {
                    text: {
                        required: false,
                    },
                },
                submitHandler: function (form) {
                    //update user info
                    //console.log(form);
                    $.post(form.action + '?rt=json', $(form).serialize(), function (data) {
                        if (data && data.res == 0) {
                            showSuccess('保存成功');
                            $self.find('.form-group').addClass('saved');
                            //location.href = CONFIG.collection.admin_list_url;

                        } else {
                            showErrorMessage(data.hint);
                        }
                    }, 'json');

                    return false;
                }
            });
        })

    });

    $('ul.ul-item').on('click', 'li .item-cancel-btn', function (e) {
        $(this).parents('li').remove();
    });
    $('ul.ul-item').on('click', 'li .item-del-btn', function (e) {
        var item_id = $(this).data('item-id');
        var product_id = $(this).data('product-id');
        //return false;
        var $self = $(this);
        $.post('/collection/album/delete', {'rt': 'json', 'item_id': item_id, 'product_id':product_id}, function (data) {
            if (data.res == 0) {
                showSuccess('删除成功');
                $self.parents('li').remove();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');

    });


    //layout  js
    function getAct(url) {
        var r = /\/admin\/([a-z]+[^\/]+)/,
            res = url.match(r);
        return res ? res[1] : '';
    }

    var act = getAct(location.href);
    //console.log(act);

    var curLink = $(".m-list a").filter(function (index) {
        return act == getAct($(this).attr('href'))
        //if ($(this).attr('href').replace(/http:\/\/[^\/]*/,'').replace(/[?#].*$/,'').replace(/\/$/, '') == location.pathname ){
        //    if(!act) {
        //        return true;
        //    }else{
        //        return act == getAct($(this).attr('href'))
        //    }
        //}
    })
    if (curLink.length > 0) {
        curLink.addClass('menu-on');
        //if(curLink.hasClass('sub-tab')){
        //    curLink.closest('.admin-sub-nav').css('display','block').parent('li').addClass('current');
        //}else{
        //    curLink.parent('li').addClass('current')
        //}
    }

    //product edit title 20
    $('#product_form').on('keyup', 'input[name=title]', function (e) {
        $(this).siblings('#num-hint').html(60 - $(this).val().replace(/[^\u0000-\u00ff]/g, "aaa").length);
        return false;
    });
    $('#product_form').on('change', 'input[name=size_ids]', function (e) {
        var size_ids = $('#product_form').find('input[name=size_ids]:checked').map(function () {
            return $(this).val();
        }).get();
        $.post(CONFIG.size.update_url, {
            "product_id": $('#product_form input[name=product_id]').val(),
            "size_ids": size_ids
        }, function (data) {

            if (data && data.res == 0) {
                //
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    });
    $('#product_form').on('change', 'input[name=color_ids]', function (e) {
        var color_ids = $('#product_form').find('input[name=color_ids]:checked').map(function () {
            return $(this).val();
        }).get();
        $.post(CONFIG.product.update_color_url, {
            "product_id": $('#product_form input[name=product_id]').val(),
            "color_ids": color_ids
        }, function (data) {

            if (data && data.res == 0) {
                //
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    });
    $('#product_form').on('change', 'input[name=tag_ids]', function (e) {
        var tag_ids = $('#product_form').find('input[name=tag_ids]:checked').map(function () {
            return $(this).val();
        }).get();
        $.post(CONFIG.tag.update_url, {
            "product_id": $('#product_form input[name=product_id]').val(),
            "tag_ids": tag_ids
        }, function (data) {

            if (data && data.res == 0) {
                //
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    });

    $('[data-toggle="tooltip"]').each(function () {
        $(this).tooltip();
    });


});
