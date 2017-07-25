<?php
/**
 *manager admin验证器，后台管理员验证器,供开发人员产考
 */
namespace app\manage\validate;

use think\Db;

class User extends Base{
    /**
     *验证规则
     */
    protected $rule = [
        'user_name'  =>  'require|max:25|unique:user|isMobile',
        'user_password'    =>  'require|max:20|min:6',
        'user_realname'    =>  'require',
    ];
    
    /**
     *验证提示消息
     */
    protected $message = [
        'user_name.require'  => '用户名必须',
        'user_name.max'      => '用户名不能超过25个字符',
        'user_name.isMobile'=> '用户名必须是手机号',
        'user_name.checkOnly'=> '用户已存在',
        'user_password.require'    => '密码必须',
        'user_password.max'        => '密码不能超过20个字符',
        'user_password.min'        => '密码不能少于6个字符',
        'user_realname.require'    => '姓名必须',
    ];
    
    /**
     *验证场景
     */
    protected $scene = [
        'add'   => ['user_name', 'user_password', 'user_realname'],
    ];
}