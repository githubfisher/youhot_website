<?php
$this->layout->load_css('admin/css/stylist.css');
?>
<div class="stylistbox">
    <div class="styllistimg">
        <!-- 页面logo -->
        <div class="kong"></div>
        <div class="addstyl">立刻加入</div>
        <div class="styllistlog"><img src="/static/admin/images/styllist1.png" alt=""></div>
        <div class="styllist2">一原创服装设计平台一</div>
        <!--输入框 -->
        <div class="styllistinput">
            <form action="" id="styl-form">
                <input type="hidden" name="rt" value="json">
            <input type="text" placeholder="品牌名称"  name="brand_name"><br>
            <input type="text" placeholder="姓名"  name="name"><br>
            <input type="text" placeholder="手机号"  name="telephone"><br>
            <input type="submit" value="马上入驻" id="submit-bottom" >
            </form>
        </div>
    </div>
</div>
<script src="/static/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script src="/static/admin/js/app.js"></script>
