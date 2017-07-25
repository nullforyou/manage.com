<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//前端路由
include_once(CONF_PATH . 'index/route.php');


Route::group('manage', function(){
	Route::rule('ie9', 'manage/Hint/ie9', 'get'); //容错页面
	
	Route::rule('login', 'manage/Sign/signin', 'get|post'); //登录
    
    Route::rule('logout', 'manage/Sign/logout', 'post'); //登出
    
    Route::rule('index', 'manage/Index/index', 'get'); //首页
    
    Route::rule('plan', 'manage/Index/plan', 'get'); //面板
	
	Route::rule('upload', 'manage/Index/_update', 'get|post'); //文件上传
	
	/**
	 *系统设置
	 */
	Route::group('settings', function(){
		Route::rule('clear', 'manage/Settings/clearCache', 'post');
		Route::rule('system/conf', 'manage/Settings/systemConfig', 'get|post'); //配置设置
		
	});
	
	/**
	 *权限-用户管理
	 */
	Route::group('power', function(){
		//权限
		Route::rule('role/list', 'manage/Auth/role', 'get'); //权限角色
		Route::rule('role/modify', 'manage/Auth/modifyRole', 'get|post'); //修改角色
		Route::rule('role/authorize', 'manage/Auth/authorize', 'get|post'); //设置权限
		/*ajax*/
		Route::rule('role/jqgridlist/:param', 'manage/Auth/getjQGridList', 'get', [], ['param'=> '\w+']);
		
		/*用户*/
		Route::rule('user/list', 'manage/User/index', 'get');
		Route::rule('user/jqgridlist/:param', 'manage/User/getjQGridList', 'get', [], ['param'=> '\w+']);
		Route::rule('user/modify', 'manage/User/modify', 'get|post'); //修改管理员
    });
	
	
	/**
	 *内容管理
	 */
	Route::group('content', function(){
		/*内容类别*/
		Route::rule('cat/list', 'manage/Category/index', 'get');
		Route::rule('cat/modify/[:id]', 'manage/Category/modify', 'get|post', [], ['id'=> '\d+']);
		
		/*内容标签*/
		Route::rule('flag/list', 'manage/Flag/index', 'get');
		Route::rule('flag/modify/[:id]', 'manage/Flag/modify', 'get|post', [], ['id'=> '\d+']);
		
		/*内容*/
		Route::rule('article/list', 'manage/Article/index', 'get');
		Route::rule('article/modify/[:id]', 'manage/Article/modify', 'get|post', [], ['id'=> '\d+']);
		Route::rule('article/pub/:oper/:id', 'manage/Article/changeStatus', 'post', [], ['oper'=> '\w+','id'=> '\d+']);
		
		/*图集*/
		Route::rule('atlas/list', 'manage/Atlas/index', 'get');
		Route::rule('atlas/modify/[:id]', 'manage/Atlas/modify', 'get|post', [], ['id'=> '\d+']);
		Route::rule('atlas/pub/:oper/:id', 'manage/Atlas/changeStatus', 'post', [], ['oper'=> '\w+','id'=> '\d+']);
		
		/*评论*/
		Route::rule('comment/list', 'manage/Comment/index', 'get');
		Route::rule('comment/pub/:oper/:id', 'manage/Comment/changeStatus', 'post', [], ['oper'=> '\w+','id'=> '\d+']);
		Route::rule('comment/subpub/:oper/:id', 'manage/Comment/subCommentChangeStatus', 'post', [], ['oper'=> '\w+','id'=> '\d+']);
		
    });
	
	/**
	 *附件管理
	 */
	Route::group('accessory', function(){
		
		/*图集*/
		Route::rule('list/[:dir]', 'manage/Accessory/file', 'get', [], ['dir'=> '\w+']);
    });
},['ext'=>'html']);

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
