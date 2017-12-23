<?php
$this->layout->load_css("admin/css/aboutus.css");
$this->layout->load_css("admin/css/manage.css");
$this->layout->placeholder('title', '申请内测');

?>
<style>
    .slogon {
        text-align: center;
        margin: 13px auto;
        color: gray;
    }

    .mt24 {
        margin-top: 24px;
    }

    .mt38 {
        margin-top: 38px;
    }

    body {
        background-color: #fff
    }
</style>
<div class="about-box">
    <div class="kongbox"></div>
    <div>
        <img src="/static/admin/images/s.png" alt="" height="66" class="center-block">
    </div>
    <div class="mt24">
        <img src="/static/admin/images/styl.png" alt="" height="18" class="center-block">
    </div>
    <div>
        <div class="slogon">
            <span>一原创服装设计平台一</span>
        </div>
    </div>
    <?php if(isset($udid)):?>
        <div style="margin-top: 139px;text-align: center;font-size: 200%;">
            <img src="/static/images/success_black.png" alt="success" width="38">
            <span><?=$udid?'提交成功':'提交失败'?></span>
        </div>

    <?php else:?>
    <div class="mt38">
        <img src="/static/admin/images/about1.png" alt="" height="284" class="center-block">
    </div>
    <div class="mt38">
        <a class="btn btn-black center-block" style="width:80%;font-size:150%" id="app-test-btn" href="http://udid.onlywish.me/getudid.php">申请内测</a>
    </div>
    <?php endif;?>


</div>


<div class="container-fluid" id="wx-popup" style="display:none;background-color: #515556;">
    <div class="row" style="height:100px">
        <div class="col-xs-2">
        </div>
        <div class="col-xs-8" style="padding: 23px 0px 0px;">
            <div style="text-align: left;color:#E6E6E6" class="pull-right">
                <p>点击右上角菜单</p>

                <p>在Safari浏览器中打开并进入应用</p>
            </div>
        </div>
        <div class="col-xs-2 " style="text-align: right;padding: 10px 12px 0px 0px">
            <img src="/static/images/arrow.png" width="44" height="48">
        </div>
    </div>
</div>

<div class="modal fade" id="qrcode-modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header clearfix">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">扫码下载</h4>
            </div>

            <div class="modal-body clearfix" style="text-align: center;">

                <img alt="Scan me!"
                     src="data:image/png;base64,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"
                     class="center-block">

                <p style="margin-top: 2em"> 目前仅iOS版本,请用iPhone扫码下载</p>

            </div>

        </div>
    </div>
</div>
<script>
    $(function () {

        function is_weixn() {
            var ua = navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == "micromessenger") {
                return true;
            } else {
                return false;
            }
        }

        $('#app-test-btn').click(function (e) {
            var platform = "<?=$is_ios ? 'ios':''?>";
            if (platform == 'ios') {
                if (is_weixn()) {
                    var html = $('#wx-popup');
                    html.prependTo('body').slideDown('slow');
                } else {
                    location.href = $(this).attr('href');
                }
            } else {  //pc or an
                $('#qrcode-modal').modal('show');
            }

            return false;
        });
    });

</script>