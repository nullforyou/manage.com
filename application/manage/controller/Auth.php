<?php
namespace app\manage\controller;
use \app\manage\controller\System;
use \app\manage\model\Role;
use \think\Config;
use \think\Db;

class Auth extends System{
    
    function __construct(){
        parent::__construct();
    }
    
    public function role(){
        return $this->fetch('auth/role',['rows' => $this->rows]);
    }
    
    public function modifyRole(){
        try {
            $model = new Role();
            $data = [];
            if ($this->request->has('role_name')) {
                $data['role_name'] = $this->request->post('role_name/s');
            }
            if ($this->request->has('role_status')) {
                $data['role_status'] = $this->request->post('role_status/s');
            }
            if ($this->request->has('remark')) {
                $data['remark'] = $this->request->post('remark/s');
            }
            if ($this->request->post('oper') == 'add') {
                $model->insertData(array_merge($data, ['user_id'=>$this->_userId]));
            } elseif ($this->request->post('oper') == 'edit') {
                if (!$this->request->has('id')) {
                    throw new \Exception('缺失操作对象');
                }
                $data['role_id'] = $this->request->post('id/d');
                $model->modifyData($data);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }
    
    public function _getList($type){
        $JqGrid = \app\manage\extend\JqGrid::instance(['model'=> new Role(),'vagueField'=>['role_name']]);
        return $JqGrid->query();
    }
    
    public function authorize(){
        $roleId = $this->request->param('role_id/d');
        if ($roleId <= 0) {
            $this->error('缺失角色对象，请重新选择角色', null, ['__token__'=>$this->request->token('__token__')]);
        }
        if ($this->request->isAjax()) {
            try {
                $auth = $this->request->post('menus/a');
                //删除所有原来的权限
                Db::name('auth_access')->where('role_id', $roleId)->delete();
                if ($auth) {
                    $data = [];
                    foreach ($auth as $val) {
                        $data[] = ['role_id'=>$roleId, 'auth_name' => $val];
                    }
                    Db::name('auth_access')->insertAll($data);
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage(), null, ['__token__'=>$this->request->token('__token__')]);
            }
            $this->success('设置成功');
        } else {
            //拉取当前角色的权限
            $authList = Db::name('auth_access')->where('role_id', $roleId)->select();
            $authList = getValByKey('auth_name', $authList, 'role_id');
            return $this->fetch('auth/authorize', [
                'role_id'=>$roleId,
                'menus'=>\app\manage\extend\Permission::instance()->getPermissionMenus($this->_userInfo['auth'], $this->_userInfo['user_super'], true),
                'authList'=>$authList
            ]);
        }
    }
    
}
