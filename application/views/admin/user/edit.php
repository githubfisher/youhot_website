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

    .choose-cover {

    }
</style>
<div class="m-center-right">
    <form id="user-edit-form" action="/user/supply_detail" class="form-horizontal">
        <input type="hidden" name="userid" value="<?= $edit_userid ?>"/>

        <div class="m-center-top">
            <div class="col-sm-4"><h3>用户资料修改</h3></div>
            <div class="col-sm-8"><h3 class="text-right">
                    <input type="submit" class="btn btn-purple" value="保存"/>
                    <input type="button" class="btn btn-light-purple" onclick="history.back()" value="取消"/>
                </h3></div>
        </div>
        <div class="m-content">


            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield1">头像</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8" style="position: relative;cursor:pointer">
                    <input name="facepic" value="<?= $edit_user['facepic'] ?>" type="hidden"/>
                    <img src="<?= $edit_user['facepic'] ? $edit_user['facepic'] . '@50h' : "/static/admin/images/touxiang.png" ?>" style="width:53px;height:53px;position: absolute;border-radius:50%;left:15px;top:-15px" data-toggle="modal" data-target="#crop-face-modal"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield1">个人背景</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8" style="position: relative;cursor:pointer;border:2px dashed #e9e9e9">
                    <input name="cover" value="<?= $edit_user['cover'] ?>" type="hidden"/>
                    <img src="<?= $edit_user['cover'] ? $edit_user['cover'] . '@190h' : "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" ?>" height="190" data-toggle="modal" data-target="#user-cover-modal" class="choose-cover"/>

                    <div class="center-btn glyphicon glyphicon-camera" style="top: 45%;text-align: center;font-size: 1.5em;"><span style="margin-left:10px">添加个人背景</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield2">用户名</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <label class="form-label"><?= substr($edit_user['username'], 0, 3).'****'.substr($edit_user['username'], 7, 4) ?></label>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield3">昵称</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <input type="text" name="nickname" value="<?= $edit_user['nickname'] ?>" id="formfield3" class="form-control"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield4">真实姓名</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <input type="text" name="name" value="<?= $edit_user['name'] ?>" id="formfield4" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label">性别</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <label class="radio-inline"><input type="radio" value="0" name="gender" <?php echo set_radio('sex', '0', $edit_user['gender'] == '0') ?> />男</label>
                    <label class="radio-inline"><input type="radio" value="1" name="gender" <?php echo set_radio('sex', '1', $edit_user['gender'] == '1') ?> />女</label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield5">出生日期</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <input type="text" name="age" value="<?= $edit_user['age'] ?>" id="formfield5" class="form-control"/>

                    <div class="desc gray">格式:1990-01-02</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield5">地区</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <input type="text" name="city" value="<?= $edit_user['city'] ?>" id="formfield5" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="field-intro">简短介绍</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <input type="text" name="introduce" value="<?= $edit_user['introduce'] ?>" id="field-intro" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="field-intro">自我介绍</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <textarea name="description" id="field-intro" rows="4" class="form-control" style="padding:5px"><?= $edit_user['description'] ?></textarea>
                </div>
            </div>
            <?php if ($has_admin_role): ?>
                <div class="form-group">
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        <label class="form-label" for="formfield6">分组</label>
                    </div>
                    <div class="col-md-8 col-sm-8 col-xs-8">
                        <select name="usertype" id="formfield6" class="form-control">
                            <option value="<?= USERTYPE_USER ?>" <?php echo set_select('usertype', USERTYPE_USER, $edit_user['usertype'] == USERTYPE_USER); ?>>用户</option>
                            <option value="<?= USERTYPE_ADMIN ?>" <?php echo set_select('usertype', USERTYPE_ADMIN, $edit_user['usertype'] == USERTYPE_ADMIN); ?>>管理员</option>
                            <option value="<?= USERTYPE_DESIGNER ?>" <?php echo set_select('usertype', USERTYPE_DESIGNER, $edit_user['usertype'] == USERTYPE_DESIGNER); ?>>设计师</option>
                            <option value="<?= USERTYPE_BUYER ?>" <?php echo set_select('usertype', USERTYPE_BUYER, $edit_user['usertype'] == USERTYPE_BUYER); ?>>买手</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        <label class="form-label" for="formfield7">权限</label>
                    </div>
                    <div class="col-md-8 col-sm-8 col-xs-8">
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
             <!--   <div class="form-group">
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        <label class="form-label" for="formfield6">调权</label>
                    </div>
                    <div class="col-md-8 col-sm-8 col-xs-8">
                        <input type="text" name="boost" value="<?= $edit_user['boost'] ?>" placeholder="调权" class="form-control"><span>排序值:<?= $edit_user['rank'] ?></span>

                        <p class="help-block">设计师专用:值越大,排序越靠前</p>
                        <input type="hidden" name="rank_other" value="<?= ($edit_user['rank'] - $edit_user['boost']) ?>">
                    </div>
                </div> -->
            <?php endif; ?>
            <div class="row" style="margin-top:19px;">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <label class="form-label" for="formfield8">密码</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <input type="text" name="password" value="" id="formfield8" class="form-control"/>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal" id="crop-face-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown " role="document">
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
    <div class="modal-dialog animated fadeInDown modal-lg" role="document">
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


