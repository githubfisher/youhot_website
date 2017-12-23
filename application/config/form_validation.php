<?php
$config = array(

    'user_signup' => array(
        array(
            'field' => 'username',
            'label' => '用户名',
            'rules' => 'trim|required|min_length[4]|max_length[20]|email_phone|prep_for_form|unique[' . TBL_USER . '.username]'
        ),
        array(
            'field' => 'password',
            'label' => '密码',
            'rules' => 'trim|alpha_dash|required|min_length[4]|max_length[16]'
        ),
        array(
            'field' => 'verify_code',
            'label' => '验证码',
            'rules' => 'trim|numeric|required|max_length[16]|code_verify[username]'
        )
    ),

    'login' => array(
        array(
            'field' => 'username',
            'label' => '用户名',
            'rules' => 'trim|required|max_length[20]|email_phone'
        ),
        array(
            'field' => 'password',
            'label' => '密码',
            'rules' => 'trim|required|min_length[4]|max_length[16]|matchs[passconf]|md5'
        )
    ),
    'user_update' => array(
        array(
            'field' => 'nickname',
            'label' => '昵称',
            'rules' => 'trim|max_length[64]'
        ),
        array(
            'field' => 'name',
            'label' => '姓名',
            'rules' => 'trim|max_length[32]'
        ),
        array(
            'field' => 'gender',
            'label' => '性别',
            'rules' => 'trim|integer'
        ),
        array(
            'field' => 'age',
            'label' => '年龄',
            'rules' => 'trim|max_length[3]'// min_length[2]
        ),

        array(
            'field' => 'city',
            'label' => '所在地',
            'rules' => ''
        ),
        array(
            'field' => 'introduce',
            'label' => '简短介绍',
            'rules' => 'max_length[64]'
        )
    ),

);
