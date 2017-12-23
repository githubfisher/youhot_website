<style>
    .form-group {
        margin-bottom: 23px;
        padding-bottom: 27px;
        border-bottom: 1px solid #f2f2f2;
    }

    .desc-del {
        cursor: pointer;
    }


    .biaoqian input {
        height: 36px;
        line-height: 36px;
    }

    .biaoqian-left {
        width: 5%;
        height: auto;
        line-height: 36px;
        float: left;
        text-align: center;
        margin-left: 15px;
        font-size: 14px;
        color: #111;
        font-weight: bold;
    }

    .biaoqian-right {
        width: 95%;
        height: auto;
        line-height: 36px;
        float: left
    }

    .biaoqian-right .biaoqian-small {
        width: 140px;
        height: 36px;
        line-height: 36px;
        float: left;
        color: #111;
    }

    .biaoqian-small input[type=checkbox] {
        margin-top: 10px;
        width: 15px;
        height: 15px;
        float: left
    }

    .controls ul, li {
        list-style-type: none
    }

    .pull-box {
        width: 100%;
        height: 40px;
    }

    .pull-box-left {
        width: 65px;
        height: 40px;
        float: left;
        line-height: 40px;
    }

    .cropit-image-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        width: 752px;
        height: 802px;
        cursor: move;
    }

    .cropit-image-background {
        opacity: .2;
        cursor: auto;
    }

    .image-size-label {
        margin-top: 10px;
    }

    button[type="submit"] {
        margin-top: 10px;
    }

    /*
     * If the slider or anything else is covered by the background image,
     * use relative or absolute position on it
     */
    input.cropit-image-zoom-input {
        position: relative;
        display: inline;
        width: 60%
    }

    /* Show load indicator when image is being loaded */
    .cropit-image-preview.cropit-image-loading .spinner {
        opacity: 1;
    }

    /* Show move cursor when image has been loaded */
    .cropit-image-preview.cropit-image-loaded {
        cursor: move;
    }

    /* Gray out zoom slider when the image cannot be zoomed */
    .cropit-image-zoom-input[disabled] {
        opacity: .2;
    }

    /* Hide default file input button if you want to use a custom button */
    input.cropit-image-input {
        visibility: hidden;
    }

    /* The following styles are only relevant to when background image is enabled */

    /* Translucent background image */
    .cropit-image-background {
        opacity: .1;
    }

    /* Style the background image differently when preview area is hovered */
    .cropit-image-background.cropit-preview-hovered {
        opacity: .2;
    }


    .btn-default {
        width: 72px;
        height: 42px;
        margin-top: 10px;
        line-height: 26px;
    }

    .zidingyi li input {
        width: auto;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
    }

    .zidingyi li {
        margin-bottom: 23px;
        display: inline-block;
    }

    .zidingyi li span {
        line-height: 34px;
    }


    .control-label {
        text-align: right;
    }

    .album-del {
        position: absolute;
        right: 0em;
    }

    div.nav {
        background-color: rgb(233, 233, 233);
        height: 35px;
        line-height: 35px;
    }

    div.nav span {
        background-color: #fff;
        height: 32px;
        margin: 10px auto auto 10px;
        padding: 0 1em;
        width: 6em;
        display: block;
    }

    .album-area {
        background-color: #F8F8F8;
        border-top: 1px solid #9D9D9D;
    }
    select{
        border: 0;
        padding: 5px;
    }
</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>编辑尺码图</h3></div>

    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>

            <div class="col-lg-12">
                <section class="box ">


                    <div class=" tab-pane fade in active" id="home-2">

                        <form id="product_form" name="product_form" class="form-horizontal" action="/admin/size/update">

                            <input type="hidden" name="id" value="<?= $info['id'] ?>"/>
                            <input type="hidden" name="userid" value="<?= $info['user'] ?>"/>
                            <input type="hidden" id="url" name="url" value="<?= $info['url'] ?>"/>
                            <input type="hidden" id="category" name="category" value="<?= $info['category'] ?>"/>
                            <input type="hidden" id="brand" name="brand" value="<?= $info['brand'] ?>"/>

                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">码图</label>
                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <img src="<?= $info['url'] ?>" width="300" height="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">分类</label>
                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" placeholder="分类" style="min-width:200px;" class="form-control" name="category"
                                               id="txtitle" value="<?= $info['category'] ?>" disabled>
                                            <div class="input-group-addon" style="padding:0;">
                                                <select onchange="check_category(this.value);">
                                                    <option value="">选择分类</option>
                                                    <?php foreach ($cates as $cate): ?>
                                                        <option value="<?= $cate['name'] ?>"><?= $cate['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-price">品牌</label>
                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" id="field-price" class="form-control" style="min-width:200px;" name="brand" value="<?= $info['brand'] ?>" disabled>
                                            <div class="input-group-addon" style="padding:0;">
                                                <select onchange="check_brand(this.value);">
                                                    <option value="">选择品牌</option>
                                                    <?php foreach ($brands as $brand): ?>
                                                        <option value="<?= $brand['nickname'] ?>"><?= $brand['nickname'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  clearfix">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="submit" class="btn btn-purple product-save-btn" style="width:8em">保存</button>
                                    <a href="<?=$this->config->item('url')['admin_size']?>" class="btn btn-light-purple " style="margin-top:10px;width:8em">返回管理</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

        </section>
    </div>
</div>
<script>
    function check_brand(value)
    {
        if (value != '') {
            document.getElementById('brand').value = value;
        } else {
            alert('请选择品牌！');
        }
    }
    function check_category(value)
    {
        if (value != '') {
            document.getElementById('category').value = value;
        } else {
            alert('请选择分类！');
        }
    }
</script>
