<?php
namespace app\manage\validate;

use think\Db;

class Flag extends Base{
    /**
     *验证规则
     */
    protected $rule = [
        'flag_name|标签' => 'require|max:100|unique:article_flag',
    ];
    
    /**
     *验证提示消息
     */
    protected $message = [];
}