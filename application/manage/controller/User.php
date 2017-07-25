<?php
namespace app\manage\controller;
use \app\manage\controller\System;
use \app\manage\model\User as userModel;
use \think\Config;
use \think\Db;

class User extends System{
    
    function __construct(){
        parent::__construct();
    }
    
    public function index(){
        return $this->fetch('user/index',[
            'rows' => $this->rows,
            'roleJson'=>json_encode(\app\manage\plugins\Search::searchRole($this->_userId, 1)),
        ]);
    }
    
    public function modify(){
        try {
            $model = new userModel();
            if ($this->request->post('oper') == 'add') {
                $model->insertData(array_merge($this->request->except('oper,id'), ['direct_leader' => $this->_userId, 'leader_grade' => $this->_userId.",".$this->_userInfo['leader_grade'], 'create_time' => time()]));
            } elseif ($this->request->post('oper') == 'edit') {
                $model->modifyData($this->request->except('oper'));
            } elseif ($this->request->post('oper') == 'del') {
                $id = $this->request->post('id');
                $object = $model::get(function($query) use ($id, $pigfarm){
                    $query->where('id', $id)->where('pigfarm_id', $pigfarm->pigfarm_id);
                });
                if ($object) {
                    $object->disabled = 1;
                    $object->save();
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('操作成功');
    }
    
    public function _getList($type){
        $JqGrid = \app\manage\extend\JqGrid::instance(['model'=> new userModel(),'vagueField'=>['user_name','user_realname'],'filtField'=>['user_password']]);
        return $JqGrid->query(['user_id|GT'=>1]);
    }
    
}
