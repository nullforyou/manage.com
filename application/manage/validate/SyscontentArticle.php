<?php
namespace app\manage\validate;

use think\Db;

class SyscontentArticle extends Base{
    /**
     *验证规则
     */
    protected $rule = [
        'article_title|标题' => 'require|max:100|unique:article_info',
        'article_category|文章类别' =>  'require|number|egt:0',
        'article_summary|文章简介' =>  'max:200',
        'article_template|文章类型' =>  'require|templateCheck',
        'article_pub_time|上线时间' =>  'require|egt:0',
        'article_if_pub|文章状态' =>  'require|putCheck',
    ];
    
    /**
     *验证提示消息
     */
    protected $message = [
        'article_template.templateCheck' => '文章类型不正确',
        'article_if_pub.putCheck' => '文章状态不正确',
    ];
    
    
    public function templateCheck($value, $rule, $data){
        if (in_array($data['article_template'], ['word', 'imgs'])) {
            return true;
        }
        return false;
    }
    
    public function putCheck($value, $rule, $data){
        if (in_array($data['article_if_pub'], ['pass', 'wait_auth', 'del'])) {
            return true;
        }
        return false;
    }
    
}