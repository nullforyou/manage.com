<?php
/**
 *定义共用数据模型
 */
namespace app\manage\model;

use think\Model;
use think\Cache;
use \think\Validate;

class Role extends Model{
    
    protected $pk = 'role_id';
    
    protected $table = 'op_auth_role';
    
    protected $autoWriteTimestamp = true;
    
    protected $createTime = 'created_at';
    
    protected $updateTime = 'updated_at';
    
    protected function initialize(){
        parent::initialize();
    }
    
    /**
     *设置密码修改器
     */
    public function setUserPasswordAttr($value){
        return md5(substr(md5($value), 0, 16));
    }
    
    public function insertData($data){
        try {
            $validate = new Validate([
                'role_name|角色名'=>'require|max:20',
                'user_id|所属用户'=>'require|number|gt:0',
            ]);
            if (!$validate->check($data)) {
                throw new \Exception($validate->getError());
            }
            $this->role_name = $data['role_name'];
            $this->role_status = empty($data['role_status']) ? 1 : $data['role_status'];
            $this->remark = empty($data['remark']) ? '' : $data['remark'];
            $this->user_id = $data['user_id'];
            if (false === $this->save($data)) {
                throw new \Exception('数据错误');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        
    }
    
    /**
     *更新
     */
    public function modifyData($data){
        try {
            $validate = new Validate([
                'role_name'=>'require|max:20'
            ]);
            if (!$validate->check($data)) {
                throw new \Exception($validate->getError());
            }
            $this->role_name = $data['role_name'];
            $this->role_status = empty($data['role_status']) ? 1 : $data['role_status'];
            $this->remark = empty($data['remark']) ? '' : $data['remark'];
            if (false === $this->save($data, ['role_id', $data['role_id']])) {
                throw new \Exception('数据错误');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}