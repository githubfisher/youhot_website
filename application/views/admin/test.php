<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>test</title>

    <script src="assets/js/angular/angular.js" type="text/javascript"></script>
    <script src="assets/js/angular-resource/angular-resource.js" type="text/javascript"></script>
    <script src="assets/js/app.js" type="text/javascript"></script>
</head>
<body>
<!--<div ng-app="formMod">-->
    <!--<div ng-controller="LoginController">-->
        <!--<form novalidate class="simple-form">-->
            <!--Name: <input type="text" ng-model="username"/><br/>-->
            <!--E-mail: <input type="email" ng-model="user.email"/><br/>-->
            <!--Gender: <input type="radio" ng-model="user.gender" value="male"/>male-->
            <!--<input type="radio" ng-model="user.gender" value="female"/>female<br/>-->
            <!--<input type="button" ng-click="reset()" value="Reset"/>-->
            <!--<input type="submit" ng-click="update(user)" value="Save"/>-->
        <!--</form>-->
        <!--<pre>user = {{username | json}}</pre>-->
        <!--<pre>master = {{master | json}}</pre>-->
    <!--</div>-->



<!--</div>-->
<div class="login-wrapper" ng-app="formMod">
    <div id="login" ng-controller="LoginController" class="login loginpage col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-3 col-sm-6 col-xs-offset-2 col-xs-8">
        <h1><a href="#" title="Login Page" tabindex="-1">Ultra Admin</a></h1>

        <form name="loginform" id="loginform"  method="post" novalidate>
            <p>
                <label for="user_login">手机号<br />
                    <input type="text" name="log" id="user_login" class="input" value="demo" size="20" ng-model="username" placeholder="请输入注册手机号" /></label>
            </p>
            <p>
                <label for="user_pass">Password<br />
                    <input type="password" name="pwd" id="user_pass" class="input" value="demo" size="20" ng-model="password" /></label>
            </p>
            <p class="forgetmenot">
                <label class="icheck-label form-label" for="rememberme">
                    <input name="rememberme" type="checkbox" id="rememberme" value="forever" class="skin-square-orange hidden" checked> Remember me</label>
            </p>

            <p>user:{{username}}</p>


            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-orange btn-block" value="Sign In" ng-click="submit(form)" />
            </p>
        </form>

        <p id="nav">
            <a class="pull-left" href="#" title="Password Lost and Found">Forgot password?</a>
            <a class="pull-right" href="ui-register.html" title="Sign Up">Sign Up</a>
        </p>


    </div>
</div>
</body>
</html>