<div class="form-group" style="color:#616161">
    账  号
    <?php
    echo form_input('username', set_value('username'), 'class="required login-input yonghuming" autocomplete="off" placeholder="手机号"');
    ?>
    <?php echo form_error('username') ?>
</div>


<div class="form-group" style="color:#616161">
    密  码<input type="password" placeholder="密码" name="password" id="password" class="login-input yonghuming" value=""/>
    <?php echo form_error('password') ?>
    <?php if ($E_fail != '') {
        echo '<div class="alert error">' . $E_fail . "</div>";
    }; ?>
</div>

<?php if ($need_captcha): ?>
    <div class="form-group">
        <label id='captcha_error' for='captcha' style='<?php if (!form_error("captcha")): ?>display:none<?php endif; ?>'>
            <?= form_error('captcha') ?>
        </label>

        <p>
            <strong>验证码：</strong><input size=20 name='captcha' id='captcha' value='' minlength='4'/>
        </p>

        <p>
            <span class="captcha"><?= $captcha ?></span>
            <a href='#' class='captcha-change'>看不清，换一个</a>
        </p>
    </div>
<?php endif; ?>
<div style="width:100%;height:20px;text-align: left;margin-top:20px;">
    <input type="checkbox"checked="checked" style="width:14px;height:14px;display: block;float:left;margin-left:20px;">记住密码

    <a href="" style="float:right;margin-right:10px;display:block;width:70px;height:20px;text-align: center;line-height:20px;font-size: 14px;color:#616161">忘记密码？</a>
</div>

<div class="form-group">
    <input type="submit" class="btn btn-primary denglu" value="登录" />
</div>
</div>
<?php
echo form_close();
?>
