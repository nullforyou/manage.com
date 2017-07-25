<?php
namespace app\manage\controller;

use \app\manage\extend\Permission;

class Index extends System{
	
	public function index(){
        return $this->fetch('public/index', [
            'userInfo'=> [],
            'menus' => Permission::instance()->getPermissionMenus($this->_userInfo['auth'], $this->_userInfo['user_super'], false)
        ]);
	}
	
	public function plan(){
		$data = [];
		return view('public/plan', $data);
	}
}