<?php
/**
 *插件形式扩展类文件,主要用于统一对外开放方便调用，游离于MC之间
 * 所有方法全部用静态方法
 */
namespace app\manage\plugins;
use \think\Db;

class Search{
    /**
     *根据条件获取角色
     */
    public static function searchRole($user_id, $status){
        $query = new \think\db\Query();
        return $query->name('auth_role')->where('user_id', $user_id)->where('role_status', $status)->field('role_id as id,role_name as title')->order('role_id desc')->select();
    }
    
}