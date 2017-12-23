accessid = ''
accesskey = ''
host = ''
policyBase64 = ''
signature = ''
callbackbody = ''
filename = ''
key = ''
expire = 0
now = timestamp = Date.parse(new Date()) / 1000;

function send_request() {
    var xmlhttp = null;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (xmlhttp != null) {
        phpUrl = '/aliyun/oss_get_sig'
        xmlhttp.open("GET", phpUrl, false);
        xmlhttp.send(null);
        return xmlhttp.responseText
    }
    else {
        alert("Your browser does not support XMLHTTP.");
    }
};

function get_signature() {
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    now = timestamp = Date.parse(new Date()) / 1000;
    console.log('get_signature ...');
    console.log('expire:' + expire.toString());
    console.log('now:', +now.toString())
    if (expire < now + 3) {
        console.log('get new sign')
        body = send_request()
        var obj = eval("(" + body + ")");
        host = obj['host']
        policyBase64 = obj['policy']
        accessid = obj['accessid']
        signature = obj['signature']
        expire = parseInt(obj['expire'])
        callbackbody = obj['callback']
        key = obj['dir']
        return true;
    }
    return false;
};

function set_upload_param(up) {
    var ret = get_signature()
    if (ret == true) {
        new_multipart_params = {
            'key': key + '${filename}',
            'policy': policyBase64,
            'OSSAccessKeyId': accessid,
            'success_action_status': '200', //让服务端返回200,不然，默认会返回204
            'callback': callbackbody,
            'signature': signature,
        };

        up.setOption({
            'url': host,
            'multipart_params': new_multipart_params
        });

        console.log('reset uploader')
        //uploader.start();
    }
}
var options = {
    runtimes: 'html5,flash,silverlight,html4',
    browse_button: 'selectfiles',
    container: document.getElementById('container'),
    flash_swf_url: 'lib/plupload-2.1.2/js/Moxie.swf',
    silverlight_xap_url: 'lib/plupload-2.1.2/js/Moxie.xap',
    filters: [
        {title: "Image files", extensions: "jpg,jpeg,gif,png,bmp"}
    ],

    url: 'http://oss.aliyuncs.com',

    init: {
        PostInit: function () {
            document.getElementById('fallback').innerHTML = '';
        },

        FilesAdded: function (up, files) {
            plupload.each(files, function (file) {
                console.log(file);
                document.getElementById('ossfile').innerHTML += '<div id="' + file.id + '" class="preview-img-ctn clearfix">' + file.name + ' (' + plupload.formatSize(file.size) + ')<b></b>'
                    + '<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
                    + '</div>';
            });

            set_upload_param(uploader);
            console.log('begin start');
            uploader.start();
            console.log('end start');
            return false;
        },

        UploadProgress: function (up, file) {
            var d = document.getElementById(file.id);
            d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";

            var prog = d.getElementsByTagName('div')[0];
            var progBar = prog.getElementsByTagName('div')[0]
            progBar.style.width = 2 * file.percent + 'px';
            progBar.setAttribute('aria-valuenow', file.percent);
        },

        FileUploaded: function (up, file, info) {
            console.log('uploaded')
            console.log(info.status)
            set_upload_param(up);
            if (info.status == 200) {
                var data = $.parseJSON(info.response);

                var preview_img = '<img src="'+data.file_url+'" class="img-thumbnail img-responsive">';
                document.getElementById(file.id).innerHTML = preview_img;
                //console.log(info);
                var position = $("#"+file.id).index();
                submit_img_to_album(g_product_id,data.file_url,position);
            }
            else {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
            }
        },

        Error: function (up, err) {
            set_upload_param(up);
            showErrorMessage(err.response);
            //document.getElementById('console').appendChild(document.createTextNode("\nError xml:" + err.response));
        }
    }
}


var cover_options = $.extend(true, {}, options, {
    browse_button: 'cover-selectfiles',
    container: document.getElementById('cover-container'),
    multi_selection: false,
    init: {
        PostInit: function () {
            document.getElementById('cover-fallback').innerHTML = '';

        },
        FilesAdded: function (up, files) {

            if ($('#cover-ossfile').children().length >= 1) {
                console.log('remove');
                $("#cover-ossfile div:first-child").remove();
            }

            plupload.each(files, function (file) {
                //if (up.files.length > 1) {
                //    console.log(up.files);
                //    up.removeFile(file);
                //}
                document.getElementById('cover-ossfile').innerHTML = '<div id="' + file.id + '" class="preview-img-ctn clearfix">' + file.name + ' (' + plupload.formatSize(file.size) + ')<b></b>'
                    + '<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
                    + '</div>';
            });

            set_upload_param(cover_uploader);
            console.log('cover begin start');
            cover_uploader.start();
            console.log('cover end start');
            return false;
            //if (up.files.length >= 1) {
            //    $('#pickfiles').hide('slow');
            //}
        },
        FileUploaded: function (up, file, info) {
            console.log('uploaded')
            console.log(info.status)
            set_upload_param(up);
            if (info.status == 200) {
                var data = $.parseJSON(info.response);

                var preview_img = '<img src="'+data.file_url+'" class="img-thumbnail img-responsive">';
                document.getElementById(file.id).innerHTML = preview_img;
                //console.log(info);
                update_product_cover(g_product_id,data.file_url);
            }
            else {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
            }
        },
    },

});

var uploader = new plupload.Uploader(options);
uploader.init();
var cover_uploader = new plupload.Uploader(cover_options);
cover_uploader.init();


function submit_img_to_album(product_id,url,position){
    var data = {
        "product_id":product_id
        ,"content":url
        ,"type":CONFIG.ALBUM_TYPE.IMAGE
        ,"position":position

    }
    $.post(CONFIG.album.add_url,data,function(res){
        if(res.res!=0){
            showErrorMessage(res.hint);
        }
    },'json');
}

function update_product_cover(product_id,url){
    var data = {
        "product_id":product_id
        ,"cover_image":url

    }
    $.post(CONFIG.product.save_url,data,function(res){
        if(res.res!=0){
            showErrorMessage(res.hint);
        }
    },'json');
}