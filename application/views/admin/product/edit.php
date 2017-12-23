<?php
$this->layout->load_js('admin/plugins/jquery-validation/js/jquery.validate.js');
$this->layout->load_js("admin/modules/cropit/jquery.cropit.js");
//$this->layout->load_js("admin/modules/cropit/vendor.js");
$this->layout->load_js('admin/plugins/tagsinput/js/bootstrap-tagsinput.js');
$this->layout->load_css('admin/plugins/tagsinput/css/bootstrap-tagsinput.css');
?>
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


</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>添加商品</h3></div>

    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>

            <div class="col-lg-12">
                <section class="box ">


                    <div class=" tab-pane fade in active" id="home-2">

                        <form id="product_form" name="product_form" class="form-horizontal">

                            <input type="hidden" name="product_id" value="<?= $product_id ?>"/>
                            <input type="hidden" name="rt" value="json"/>
                            <input type="hidden" name="status" value="<?= PRODUCT_STATUS_DRAFT ?>"/>


                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">商品名称</label>

                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="商品名称" style="" class="form-control" name="title"
                                               id="txtitle" value="<?= $product['title'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 60 - strlen($product['title']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('title') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--价格一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-price">商品金额</label>

                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" id="field-price" class="form-control" name="price" value="<?= $product['price'] ?>" integer required>

                                            <div class="input-group-addon">元</div>
                                        </div>
                                        <div>
                                            <span class="error"><?= form_error('price') ?></span>
                                        </div>
                                    </div>

                                    <!--
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" class="control-label" for="field-1">预定金额</label>

                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="presale_price" value="<?= $product['presale_price'] ?>" integer required>

                                            <div class="input-group-addon">元</div>
                                        </div>
                                        <div>
                                            <span class="error" style="float:left;"><?= form_error('presale_price') ?></span>
                                        </div>
                                    </div>
                                    -->
                                </div>
                            </div>

                            <!--最大限量一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">库存</label>

                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="inventory" id="input-presale_maximum" value="<?= $product['inventory'] ?>" min="0" integer>

                                            <div class="input-group-addon">件</div>
                                        </div>
                                        <div>
                                            <label class="error"><?= form_error('presale_maximum') ?></label>
                                        </div>
                                    </div>
                            <!--
                                    <label for="input-presale_minimum" class="control-label col-sm-2 col-md-2 col-xs-2 require">最小数量</label>

                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="presale_minimum" id="input-presale_minimum" value="<?= $product['presale_minimum'] ?>" min="0" integer>

                                            <div class="input-group-addon">件</div>
                                        </div>
                                        <div>
                                            <label class="error"><?= form_error('presale_minimum') ?></label>
                                        </div>
                                    </div>
                             -->

                                </div>
                            </div>
                            <!--生产周期-->
                            <!--
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-presale-days">生产周期</label>

                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="production_days" value="<?= $product['production_days'] ?>">

                                            <div class="input-group-addon">天</div>
                                        </div>
                                        <div>
                                            <label class="error"><?= form_error('production_days') ?></label>
                                        </div>
                                    </div>

                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-presale-days">预定天数</label>

                                    <div class="col-sm-3 col-md-3 col-xs-10">
                                        <div class="input-group">
                                            <input type="text" placeholder="0" id="field-presale-days" class="form-control" name="presale_days" value="<?= $product['presale_days'] ?>" min="0" integer>

                                            <div class="input-group-addon">天</div>
                                        </div>
                                        <div>
                                            <span class="error"><?= form_error('presale_days') ?></span>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            -->

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">分类</label>

                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <?php
                                        // var_dump($category_list);
                                        $cat_options = array();
                                        $first_cat = array();
                                        $thtml = array();
                                        foreach ($category_list as $cat) {
                                            if ($cat['parent_id'] != 1) {
                                                $se = ($product['category'] == $cat['id']) ? 'selected' : '';
                                                $cat_options[$cat['parent_id']][] = sprintf('<option value="%s" %s>%s</option>', $cat['id'], $se, $cat['name']);
                                            } else {
                                                $first_cat[$cat['id']] = sprintf('<optgroup label="%s" data-id="%s">', $cat['name'], $cat['id']);
                                            }
                                        }
                                        foreach ($first_cat as $key => $val) {
                                            $thtml[] = $val;
                                            $thtml = array_merge($thtml, $cat_options[$key]);
                                            $thtml[] = '</optgroup>';
                                        }
                                        echo '<select name="category" class="form-control" data-pid="" aria-invalid="false">';
                                        echo implode($thtml);
                                        echo '</select>';
                                        //                                                    echo form_dropdown("category", $cat_options, $product['category'], 'class=form-control');
                                        ?>
                                    </div>

                                </div>
                            </div>

                            <!--标签-->
                            <div class="form-group" id="biaoqian" style="line-height:36px;font-size:14px;">
                                <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">标签</label>

                                <div class="col-sm-8 col-md-8 col-xs-8">
                                    <div class="biaoqian-right">
                                        <?php foreach ($tag_list as $tag): ?>
                                            <?php
                                            $search_tag = array('id' => $tag['id'], 'name' => $tag['name'], 'description' => $tag['description']);
                                            $selected = (in_array($search_tag, $product['tags'])) ? "checked" : "";
                                            if( $selected=='' ){
                                                continue;
                                            }
                                            ?>
                                            <div class="biaoqian-small"><input name="tag_ids" type="checkbox" value="<?= $tag['id'] ?>" <?= $selected ?>><?= $tag['name'] ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!--尺码-->
                            <div class="form-group" id="format" style="line-height:36px;">

                                <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-11">尺码</label>

                                <div class="col-sm-8 col-md-8 col-xs-8">
                                    <div class="biaoqian-right" id="size-ctner">
                                        <?php foreach ($size_list as $size): ?>
                                            <?php
                                            $search_size = array('size_id' => $size['size_id'], 'name' => $size['name'], 'description' => $size['description']);

                                            $selected = (in_array($search_size, $product['available_size'])) ? "checked" : "";
                                            if( $selected=='' ){
                                                continue;
                                            }
                                            /*
                                            <div class="biaoqian-small" data-cat="<?= $size['cat_id'] ?>"><input type="checkbox" name="size_ids" value="<?= $size['size_id'] ?>" <?= $selected ?>><?= $size['name'] ?></div>
                                            */
                                            ?>
                                            <div style="color: #111; float: left; height: 36px; line-height: 36px; width: 140px;" data-cat="<?= $size['cat_id'] ?>"><input type="checkbox" name="size_ids" value="<?= $size['size_id'] ?>" <?= $selected ?>><?= $size['name'] ?></div>
                                        <?php endforeach; ?>
                                        <label for="size_ids" class="error"></label>

                                        <div id="format-size-img" style="color:#fff;display: block;width:55px;float:left;text-align: center;line-height:25px;margin-top:5px;
                                                        height:25px;background:#EF338B;border-radius:12px;cursor:pointer;position: relative">尺码表
                                            <div class="img-chima" style="width:500px;height:224px;position:absolute;bottom: -195px;right: 54px;display: none;z-index:1000;">
                                                <img src="/static/admin/images/chima.png" alt="" style="width:100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--颜色-->
                            <div class="form-group">
                                <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-color">颜色</label>

                                <div class="col-sm-8 col-md-8 col-xs-8">
                                    <div class="biaoqian-right">

                                        <?php foreach ($color_list as $color): ?>
                                            <?php
                                            $search_color = array('color_id' => $color['color_id'], 'name' => $color['name'], 'image' => $color['image']);
                                            $selected = (in_array($search_color, $product['available_color'])) ? "checked" : "";
                                            if( $selected=='' ){
                                                continue;
                                            }
                                            ?>
                                            <div class="biaoqian-small"><label><input type="checkbox" name="color_ids" value="<?= $color['color_id'] ?>" <?= $selected ?>> <span><?= $color['name'] ?></span>
                                                    <img src="<?= $color['image'] ?>" class="img-thumbnail" style="height:25px"/>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="biaoqian-small">
                                            <a href="#" id="color-add-btn" class="btn btn-black btn-xs">+ 自定义</a>

                                            <div data-url="<?= $this->config->item('url')['color_add'] ?>" id="color-add-form" style="display:none;position: absolute;">
                                                <input type="text" name="name" placeholder="颜色名称" style="line-height: 22px;padding: 4px;}" />
                                                <input type="hidden" name="image"/>
                                                <img data-toggle="modal" data-target="#color-modal" alt="选图" title="选择配图" width="32" height="32" src="http://product-album.img-cn-hangzhou.aliyuncs.com/static/img/color-img.png" style="cursor:pointer;"/>
                                                <button id="color-submit" type="button" class="btn btn-black btn-xs">确定</button>
                                            </div>
                                        </div>
                                        <label class="error" for="color_ids"></label>
                                    </div>
                                </div>

                            </div>


                            <div class="form-group">
                                <label class="control-label col-sm-2 col-md-2" for="field-1">图片</label>

                                <div class="col-sm-8" style="border: 1px solid #9D9D9D;">
                                    <div class="nav row">
                                        <span class="">图片上传</span>
                                    </div>

                                    <div class="row" style="padding-top:1em">
                                        <?php
                                        if (!empty($product['album'])) {
                                            foreach ($product['album'] as $album) {
                                                if ($album['type'] != ALBUM_RESOURCE_TYPE_VIDEO) {
                                                    continue;
                                                }
                                                $product['video'] = $album['content'];
                                                $product['video_item_id'] = $album['id'];
                                            }
                                        };
                                        ?>
                                        <label class="col-sm-3 text-right">视频地址</label>

                                        <div class="col-sm-9" style="margin-left:-2px">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <input type="hidden" name="album_id" value="<?= element('video_item_id', $product, '') ?>">
                                            <input type="hidden" name="type" value="<?= ALBUM_RESOURCE_TYPE_VIDEO ?>">
                                            <input type="hidden" name="position" value="1000">
                                            <input type="hidden" name="sell_type" value="2">
                                            <input style="width:90%" class="form-control" name="content" id="album-video" type="text" value="<?= element('video', $product, "") ?>" placeholder="播放地址">
                                            <label id="content-error" class="error" for="content" style="float: none;"></label>

                                        </div>
                                    </div>
                                    <div class="row  album-area">
                                        <div class="col-sm-4 col-md-4 col-xs-12">
                                            <div style="padding: 10px 0;">
                                                封面图(750*800)
                                            </div>
                                            <div class="col-cover-image">
                                                <input type="hidden" name="cover_image" value="<?= $product['cover_image'] ? $product['cover_image'] : '' ?>"/>
                                                <img id="p-cover-image" src="<?= $product['cover_image'] ? $product['cover_image'] . '@136h_1wh' : '' ?>"/>
                                                <span class="btn btn-half-black btn-nocorner btn-xs center-btn" data-toggle="modal" data-target="#crop-image-modal" style="color: black;top: 50%;width: 6em;">文件上传</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-8 col-md-8 col-xs-12" style="border-left: 1px solid #e1e1e1">
                                            <div style="padding: 10px 0;">
                                                内页图(750*900)
                                                                    <span class="col-cover-image" data-toggle="modal" data-target="#crop-album-modal">
                                                                        <span class="btn btn-half-black btn-nocorner btn-xs" style="color: black;">继续添加</span>
                                                                    </>
                                            </div>
                                            <div id="ossfile" class="clearfix">
                                                <?php
                                                if (!empty($product['album'])):?>
                                                    <?php foreach ($product['album'] as $album): ?>
                                                        <?php if ($album['type'] == ALBUM_RESOURCE_TYPE_IMAGE): ?>
                                                            <div id="album-image" data-pid="<?= $product['id'] ?>" data-item-id="<?= $album['id'] ?>" class="preview-img-ctn clearfix"><img
                                                                    src="<?= $album['content'] ?>@136h_1wh"
                                                                    class="img-responsive"></div>
                                                        <?php elseif ($album['type'] == ALBUM_RESOURCE_TYPE_VIDEO): ?>
                                                            <?php
                                                            $product['video'] = $album['content'];
                                                            $product['video_item_id'] = $album['id'];

                                                            ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label col-sm-2 col-md-2" for="field-1">商品详情</label>

                                <div class="col-sm-8 col-md-8 col-xs-8">
                                    <div>
                                        <ul class="zidingyi" id="desc-ctner">
                                            <?php if (empty($product['description'])): ?>
                                                <li><input type="text" placeholder="产地" name="desc_title[]" class="col-sm-5"><span class="col-sm-1">-</span><input type="text" placeholder="北京" name="desc_content[]" class="col-sm-5"></li>
                                                <li><input type="text" placeholder="材质" name="desc_title[]" class="col-sm-5"><span class="col-sm-1">-</span><input type="text" placeholder="红色" name="desc_content[]" class="col-sm-5"></li>
                                                <li><input type="text" placeholder="品牌" name="desc_title[]" class="col-sm-5"><span class="col-sm-1">-</span><input type="text" placeholder="红色" name="desc_content[]" class="col-sm-5"></li>

                                            <?php else: ?>
                                                <?php foreach ($product['description'] as $key => $desc_item) : ?>
                                                    <li><input name="desc_title[]" style="" id="tone" value="<?= $desc_item['title'] ?>" class="col-sm-5"><span class="col-sm-1">-</span><input class="col-sm-5" name="desc_content[]" value="<?= $desc_item['content'] ?>" id="tone1"/></li>

                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                        <button type="button" name="desc-add" value="" class="btn btn-black btn-xs">+ 自定义</button>

                                    </div>
                                </div>

                            </div>

                            <?php if ($this->is_admin): ?>
                                <div class="form-group ">
                                    <div class="controls">
                                        <label class="control-label col-sm-2 col-md-2" for="field-presale-days">排序值</label>

                                        <div class="col-sm-8 col-md-8 col-xs-12">
                                            <input type="text" placeholder="0" id="field-presale-days" class="form-control" name="rank" value="<?= $product['rank'] ?>" min="0" integer>
                                            <span class="error"><label class="error"><?= form_error('rank') ?></label></span>
                                        </div>
                                        <div class="col-sm-2 col-md-2 col-xs-2">
                                            <span>值越大,排序越靠前</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->is_admin): ?>
                                <div class="form-group ">
                                    <div class="controls">
                                        <label class="control-label col-sm-2 col-md-2" for="field-presale-days">Jump-URL </label>

                                        <div class="col-sm-8 col-md-8 col-xs-12">
                                            <a class="form-control" href="<?= $product['m_url'] ?>" target="_blank"><?= $product['m_url'] ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $tmp_img = json_decode($product['tmp_img'], true);
                                if( ! isset($tmp_img['jurl']) ) $tmp_img['jurl'] = 'None';
                                ?>
                                <div class="form-group ">
                                    <div class="controls">
                                        <label class="control-label col-sm-2 col-md-2" for="field-presale-days">URL </label>

                                        <div class="col-sm-8 col-md-8 col-xs-12">
                                            <a class="form-control" href="<?= $tmp_img['jurl'] ?>" target="_blank"><?= substr($tmp_img['jurl'],0,75).'......' ?></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group  clearfix">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="submit" class="btn btn-purple product-save-btn" style="width:8em">保存</button>
                                    <a target="_blank" href="/product/detail?rt=mobile&product_id=<?= $product['id'] ?>" class="btn btn-light-purple " style="margin-top:10px;width:8em">预览</a>

                                </div>
                            </div>


                        </form>

                    </div>
                </section>
            </div>

        </section>
    </div>
</div>

<div class="modal" id="crop-image-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ultraModal-Label">商品封面图片<span class="desc" style="margin-left:2em;color:gray;font-size:80%">尺寸750*800</span></h4>
            </div>
            <div class="modal-body">

                <div id="image-cropper" class="sub-section image-cropper image-background-border">
                    <form action="" id="cover-image-form" method="">
                        <div class="container-fluid">

                            <div class="demo">
                                <div class="column">
                                    <div class="cropit-image-preview-container">

                                        <div class="cropit-image-preview" style="">

                                            <div class="spinner">
                                                <div class="spinner-dot">
                                                </div>
                                                <div class="spinner-dot">
                                                </div>
                                                <div class="spinner-dot">
                                                </div>
                                            </div>
                                            <div class="error-msg">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slider-wrapper">
                                        <span class="glyphicon glyphicon-picture"></span>
                                        <input type="range" class="cropit-image-zoom-input custom">
                                        <span class="glyphicon glyphicon-picture picture-large"></span>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="btns">
                                        <input type="file" name="file" class="cropit-image-input custom">

                                        <div class="btn select-image-btn">
                                            <span class="glyphicon glyphicon-picture "></span> 选择新图片
                                        </div>
                                        <div class="btn upload-btn">
                                            <span class="glyphicon glyphicon-cloud-upload"></span>
                                            上传
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <input type="hidden" name="filename">
                            <input type="hidden" name="file_content">
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">关闭</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="crop-album-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ultraModal-Label">商品展示图片<span class="desc" style="margin-left:2em;color:gray;font-size:80%">尺寸750*900</span></h4>
            </div>
            <div class="modal-body">

                <div id="album-image-cropper" class="sub-section image-cropper image-background-border">
                    <form action="" id="album-image-form" method="">
                        <div class="container-fluid">

                            <div class="demo">
                                <div class="column">
                                    <div class="cropit-image-preview-container">
                                        <div class="cropit-image-preview" style="height: 902px;">

                                            <div class="spinner">
                                                <div class="spinner-dot"></div>
                                                <div class="spinner-dot"></div>
                                                <div class="spinner-dot"></div>
                                            </div>
                                            <div class="error-msg">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="slider-wrapper">
                                        <span class="glyphicon glyphicon-picture"></span>
                                        <input type="range" class="cropit-image-zoom-input custom">
                                        <span class="glyphicon glyphicon-picture picture-large"></span>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="btns">
                                        <input type="file" name="file" class="cropit-image-input custom">

                                        <div class="btn select-image-btn">
                                            <span class="glyphicon glyphicon-picture "></span>添加新图片
                                        </div>
                                        <div class="btn upload-btn">
                                            <span class="glyphicon glyphicon-cloud-upload"></span>
                                            上传
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <input type="hidden" name="filename">
                            <input type="hidden" name="file_content">
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="color-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown " role="document">
        <div class="modal-content">
            <div class="modal-body">

                <div id="color-cropper" class="sub-section image-cropper image-big-cropper image-background-border">
                    <form action="" id="" method="">
                        <div class="container-fluid">

                            <div class="demo">
                                <div class="column">
                                    <div class="cropit-image-preview-container">

                                        <div class="cropit-image-preview" style="width: 80px;height:80px">

                                            <div class="spinner">
                                                <div class="spinner-dot"></div>
                                                <div class="spinner-dot"></div>
                                                <div class="spinner-dot"></div>
                                            </div>
                                            <div class="error-msg">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slider-wrapper">
                                        <span class="glyphicon glyphicon-picture"></span>
                                        <input type="range" class="cropit-image-zoom-input custom">
                                        <span class="glyphicon glyphicon-picture picture-large"></span>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="btns">
                                        <input type="file" name="file" class="cropit-image-input custom">

                                        <div class="btn select-image-btn btn-primary">
                                            <span class="glyphicon glyphicon-picture"></span> 选择新图片
                                        </div>
                                        <div class="btn upload-btn btn-success">
                                            <span class="glyphicon glyphicon-cloud-upload"></span>
                                            上传
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="filename">
                        <input type="hidden" name="file_content">
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">关闭</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        var chima = $("#format-size-img");
        var footage = $(".img-chima");
        chima.click(function () {
            footage.toggle('slow');
        })


        $('select[name=category]').change(function (e) {
            var cat_id = $(':selected', this).parent().data('id');
            console.log(cat_id);
            $("#size-ctner div.biaoqian-small").show().not('[data-cat=' + cat_id + ']').hide().find('input[name=size_ids]').attr('checked', false);
        })
        $('select[name=category]').trigger('change');

//        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
//            if($(e.target).attr('href')=='#profile-2'){
//                $('#product_form').trigger('submit')
//            } // newly activated tab
////            console.log(e.relatedTarget) // previous active tab
//        })

    })
</script>
