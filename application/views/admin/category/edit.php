<?php
$this->layout->load_js('admin/plugins/jquery-validation/js/jquery.validate.js');
$this->layout->placeholder('title', '用户资料编辑');
$this->layout->load_js("admin/modules/cropit/jquery.cropit.js");
?>


<style>
    .form-label {
        font-size: 14px;
        color: #111111;
        line-height: 40px
    }

    .col-md-2, .col-sm-2, .col-xs-2 {
        text-align: right
    }

    .col-md-10 {
        margin-top: 10px
    }

    .m-right-btn {
        height: 30px
    }
</style>
<div class="m-center-right">
    <form id="user-edit-form" action="/user/supply_detail">
        <input type="hidden" name="userid" value="<?= $edit_userid ?>"/>

        <div class="m-center-top">
            <p>用户资料修改</p>
        </div>
        <div class="m-right-btn">
            <span style="color:#000;font-size:12px;margin-left:20px;line-height: 20px;">查看修改用户的权限</span>

            <div class="pull-right">
                <input type="submit" class="btn btn-success" value="保存修改" style="background:#EC1379;border:1px solid #EC1379;margin-right:8px;margin-top:-25px"/>
                <input type="button" class="btn" onclick="history.back()" value="取消" style="margin-right: 20px;margin-top:-25px;background:#EA3D3D;color:#fff"/>
            </div>
        </div>
        <div class="m-content">
            <section class="wrapper" style='width:100%;'>
                <div class="col-lg-12">
                    <section class="box ">
                        <header class="panel_header" style="height:41px;background:#F4F5F9">
                            <h2 class="title pull-left" style="line-height:20px">编辑用户</h2>
                        </header>

                        <div class="content-body" style="padding-right:60px">

                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield1">头像</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10" style="position: relative;cursor:pointer">
                                    <input name="facepic" value="<?=$edit_user['facepic']?>" type="hidden" />
                                    <img src="<?= $edit_user['facepic'] ? $edit_user['facepic'].'@50h' : "/static/admin/images/touxiang.png" ?>" style="width:53px;height:53px;position: absolute;border-radius:50%;left:15px;top:-15px"  data-toggle="modal" data-target="#crop-face-modal"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield1">个人背景</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10" style="position: relative;cursor:pointer">
                                    <input name="cover" value="<?=$edit_user['cover']?>" type="hidden" />
                                    <img src="<?= $edit_user['cover'] ? $edit_user['cover'].'@200h' : "/static/admin/images/touxiang.png" ?>" style="height:200px;" class="img-thumbnail"  data-toggle="modal" data-target="#user-cover-modal"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield2">用户名</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <p><?= $edit_user['username'] ?></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield3">昵称</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="text" name="nickname" value="<?= $edit_user['nickname'] ?>" id="formfield3"/>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield4">真实姓名</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="text" name="name" value="<?= $edit_user['name'] ?>" id="formfield4"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label">性别</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="radio" value="0" name="gender" <?php echo set_radio('sex', '0', $edit_user['gender'] == '0') ?> />男
                                    <input type="radio" value="1" name="gender" <?php echo set_radio('sex', '1', $edit_user['gender'] == '1') ?> />女
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield5">出生日期</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="text" name="age" value="<?= $edit_user['age'] ?>" id="formfield5"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield5">所在城市</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="text" name="city" value="<?= $edit_user['city'] ?>" id="formfield5"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="field-intro">简短介绍</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="text" name="introduce" value="<?=$edit_user['introduce']?>" id="field-intro" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="field-intro">自我介绍</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <textarea name="description" id="field-intro" rows="4" style="padding:5px"><?=$edit_user['description']?></textarea>
                                </div>
                            </div>
                            <?php if($has_admin_role):?>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield6">分组</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <select name="usertype" id="formfield6">
                                        <option value="<?= USERTYPE_USER ?>" <?php echo set_select('usertype', USERTYPE_USER, $edit_user['usertype'] == USERTYPE_USER); ?>>用户</option>
                                        <option value="<?= USERTYPE_ADMIN ?>" <?php echo set_select('usertype', USERTYPE_ADMIN, $edit_user['usertype'] == USERTYPE_ADMIN); ?>>管理员</option>
                                        <option value="<?= USERTYPE_DESIGNER ?>" <?php echo set_select('usertype', USERTYPE_DESIGNER, $edit_user['usertype'] == USERTYPE_DESIGNER); ?>>设计师</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield7">权限</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="hidden" name="role" value="<?= $edit_user['role'] ?>"/>

                                    <?php
                                    $i = 1;
                                    foreach (get_admin_roles() as $role => $desc) {

                                        echo "<label style='width:100px;height:26px '><span style='margin-left:10px;'>";
                                        echo form_checkbox('role-check', $role, ($role & (int)$edit_user['role']));
                                        echo $desc;
                                        echo "</span></label>";
                                        if ($i % 3 == 0) {
                                            echo "<br>";
                                        }
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                                <div class="row">
                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                        <label class="form-label" for="formfield6">排序值</label>
                                    </div>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        <input type="text" name="rank" value="<?=$edit_user['rank']?>" placeholder="排序值"><p class="help-block">设计师专用:值越大,排序越靠前</p>
                                    </div>
                                </div>
                            <?php endif;?>
                            <div class="row" style="margin-top:19px;">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label class="form-label" for="formfield8">密码</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-10">
                                    <input type="text" name="password" value="" id="formfield8"/>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </form>
</div>
<div class="modal" id="crop-face-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown " role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ultraModal-Label">头像<span class="desc" style="margin-left:2em;color:gray;font-size:80%"></span></h4>
            </div>
            <div class="modal-body">

                <div id="face-cropper" data-cid="<?= element('userid', $edit_user, 0) ?>" class="sub-section image-cropper image-big-cropper image-background-border">
                    <form action="" id="" method="">
                        <div class="container-fluid">

                            <div class="demo">
                                <div class="column">
                                    <div class="cropit-image-preview-container">

                                        <div class="cropit-image-preview" style="width: 180px;height:180px">

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
        </div>
    </div>
</div>
<div class="modal" id="user-cover-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ultraModal-Label">个人展示图片<span class="desc" style="margin-left:2em;color:gray;font-size:80%">尺寸750*395</span></h4>
            </div>
            <div class="modal-body">

                <div id="user-cover-cropper" data-cid="<?= element('userid', $edit_user, 0) ?>" class="sub-section image-cropper image-big-cropper image-background-border">
                    <form action="" id="" method="">
                        <div class="container-fluid">

                            <div class="demo">
                                <div class="column">
                                    <div class="cropit-image-preview-container">

                                        <div class="cropit-image-preview" style="width: 750px;height:395px">

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
        </div>
    </div>
</div>


