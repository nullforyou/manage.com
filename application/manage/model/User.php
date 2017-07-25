<?php
/**
 *定义共用数据模型
 */
namespace app\manage\model;

use think\Model;
use think\Cache;
use \think\Validate;
use \think\Db;

class User extends Model{
    
    protected $pk = 'user_id';
    
    protected $table = 'op_user';
    
    protected function initialize(){
        parent::initialize();
    }
    
    /**
     *设置密码修改器
     */
    public function setUserPasswordAttr($value){
        return md5(substr(md5($value), 0, 16));
    }
    
    /**
     *登录
     */
    public function signIn($username, $password){
        $userInfo = $this->where('user_name', $username)->where('user_status', 1)->field('user_id,user_name,user_password,user_realname,user_status,user_super,role,leader_grade,login_error_num,login_error_time')->find();
        if (empty($userInfo)) {
            return false;
        }
        if ($userInfo->login_error_num >= 5) {
            return 0;
        }
        $user = User::get($userInfo->user_id);
        if (0 !== strcmp(md5(substr(md5($password), 0, 16)), $userInfo->user_password)) {
            if ($userInfo->login_error_time + 86400 < time()) {
                $user->login_error_num     = 1;
                $user->login_error_time    = time();
            } else {
                $user->login_error_num ++;
                $user->login_error_time    = time();
            }
            $user->save();
            if ($user->login_error_num >= 3) {
                return 5 - $user->login_error_num;
            } else {
                return false;
            }
        }
        $user->login_error_num     = '0';
        $user->login_error_time    = '0';
        $user->save();
        
        //获取权限
        $auth = [];
        if ($userInfo->user_super != 1 && Db::name('auth_role')->where('role_id', $userInfo->role)->where('role_status', 1)->count()) {
            $auth = Db::name('auth_access')->where('role_id', $userInfo->role)->column('auth_name');
        }
        $userInfo->auth = implode(',', $auth);
        return [
			'user_id' => $userInfo->user_id,
            'user_name' => $userInfo->user_name,
            'user_realname' => $userInfo->user_realname,
            'user_super' => $userInfo->user_super,
			'leader_grade' => $userInfo->leader_grade,
			'auth' => $userInfo->auth,
        ];
    }
    
    public function insertData($data){
        try {
            if (false === $this->validate('User.add')->allowField(true)->save($data)) {
                throw new \Exception($this->getError());
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     *更新
     */
    public function modifyData($data){
        //使用独立验证，先过滤要修改的属性
        if (empty($data['user_id'])) {
            return false;
        }
        
        $modifyField = [];
        $rules = [];
        $message = [];
        if (!empty($data['user_password'])) {
            $modifyField['user_password'] = $data['user_password'];
            $rules['user_password'] = 'max:20|min:6';
            $message['user_password.min'] = '密码必须大于6个字符';
            $message['user_password.max'] = '密码必须小于20个字符';
        }
        if (!empty($data['user_realname'])) {
            $rules['user_realname'] = 'require';
            $modifyField['user_realname'] = $data['user_realname'];
        }
        if (isset($data['user_status'])) $modifyField['user_status'] = $data['user_status'];
        if (isset($data['user_email'])) $modifyField['user_email'] = $data['user_email'];
        if (isset($data['role'])) {
            $modifyField['role'] = $data['role'];
        }
        
        if (empty($modifyField)) {
            return true;
        }
        try {
            Db::startTrans();
            $validate = new Validate($rules, $message);
            if (!$validate->check($modifyField)) {
                throw new \Exception($validate->getError());
            }
            if (false === $this->allowField(true)->save($modifyField, ['user_id'=>$data['user_id']])) {
                throw new \Exception('数据错误');
            }
            if (isset($data['pigfarm_name'])) {
                $pigfarm = new Pigfarm();
                $pigfarm_id = $pigfarm->where('user_id', $data['user_id'])->value('pigfarm_id');
                $result = $pigfarm->validate([
                    'pigfarm_name|猪场名称' => 'require|max:20|unique:pigfarm',
                ])->allowField(true)->save(['pigfarm_name'=>$data['pigfarm_name'],'pigfarm_id'=>$pigfarm_id], ['pigfarm_id'=>$pigfarm_id]);
                if (false === $result) {
                    throw new \Exception($pigfarm->getError());
                }
            }
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
        Db::commit();
    }
}