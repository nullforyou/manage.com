<?php
/**
 *公用场景验证器 - 基础类
 */
namespace app\manage\validate;

use think\Validate;
use think\Db;
use think\Session;

class Base extends Validate{
    
    public function __construct(array $rules = [], $message = [], $field = []){
        //parent::__construct($rules, array_merge($message, [
        //    'pigfarm_id.checkFarmPopedom'=>'农场不在操作范围',
        //]), $field);
        
    }
    
    /**
     *自定义验证规则，验证手机号
     */
    protected function isMobile($value, $rule, $data){
        if (preg_match('/^1[34578]\d{9}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }
    
}