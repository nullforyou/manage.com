<?php
namespace app\manage\validate;

use think\Db;

class SyscontentCategory extends Base{
    /**
     *验证规则
     */
    protected $rule = [
        'category_name|分类名称' => 'require|chsDash|max:50|unique:article_category|disabledCheck|enabledCheck',
        'disabled|使用状态' =>  'require|number|egt:0',
        'category_pid|上级分类' =>  'require|number|egt:0',
        'category_tag|分类标签' =>  'require|alphaDash|max:50|unique:article_category',
        'category_level|分类层级' =>  'require|number|between:1,3',
    ];
    
    /**
     *验证提示消息
     */
    protected $message = [
        'category_name.disabledCheck' => '该分类存在有效子集，不能删除',
        'category_name.enabledCheck' => '该分类父级为不可用状态，不能激活',
    ];
    
    /**
     *删除检查
     */
    protected function disabledCheck($value, $rule, $data){
        if (empty($data['category_id'])) {
            return true;
        }
        if (!Db::name('article_category')->where('category_id', $data['category_id'])->value('disabled')) {
            //未改变状态
            return true;
        }
        if (Db::name('article_category')->where('category_pid', $data['category_id'])->where('disabled', 0)->count()) {
            return false;
        }
        return true;
    }
    /**
     *恢复检查
     */
    protected function enabledCheck($value, $rule, $data){
        if (empty($data['category_id'])) {
            return true;
        }
        if (Db::name('article_category')->where('category_id', $data['category_id'])->value('disabled')) {
            //未改变状态
            return true;
        }
        //没有父级
        if (empty($data['category_pid'])) {
            return true;
        }
        //检查上级是否被删除
        if (Db::name('article_category')->where('category_id', $data['category_pid'])->where('disabled', 1)->count()) {
            return false;
        }
        return true;
    }
}