<?php
return [
    /*不用登陆就能访问的方法*/
    'no_login_method' => ['manage/Sign/signin'],
    
    /*不用授权就能访问的类*/
    'no_auth_class' => ['manage/AjaxSearch'],
    
    /*不用授权就能访问的方法*/
    'no_auth_method' => ['manage/Sign/signin', 'manage/Sign/logout', 'manage/Index/index', 'manage/Index/plan'],
    
    /*菜单权限配置*/
    'menus' => [
        'settings' => array(
            'label' => '系统设置',
            'icon'  => 'fa-gears',
            'menu'  => array(
                array('label' => '配置设置','display'=>true, 'as' => 'settings.system.conf', 'action' => 'manage/Settings/systemConfig', 'url' => '/manage/settings/system/conf', 'method' => 'get|post'),
                
               array('label' => '清除系统缓存','display'=>false, 'as' => 'settings.clear.cache', 'action' => 'manage/Settings/clearCache', 'method' => 'post'),
            ),
        ),
        
        
        'power' => array(
            'label' => '权限用户管理',
            'icon'  => 'fa-cog',
            'menu'  => array(
                array('label' => '角色列表','display'=>true, 'as' => 'power.role.list', 'action' => 'manage/Auth/role', 'url' => '/manage/power/role/list', 'method' => 'get'),
                array('label' => 'ajax加载角色数据','display'=>false, 'as' => 'auth.roles.list.ajax', 'action' => 'manage/Auth/getjQGridList', 'method' => 'get'),
                array('label' => '角色操作','display'=>false, 'as' => 'auth.role.modify', 'action' => 'manage/Auth/modifyRole', 'method' => 'get|post'),
                array('label' => '设置权限','display'=>false, 'as' => 'auth.roles.authorize', 'action' => 'manage/Auth/authorize', 'method' => 'get|post'),

                
                array('label' => '管理员列表','display'=>true, 'as' => 'power.user.list', 'action' => 'manage/User/index', 'url' => '/manage/power/user/list', 'method' => 'get'),
                array('label' => 'ajax加载管理员数据','display'=>false, 'as' => 'user.list.ajax', 'action' => 'manage/User/getjQGridList', 'method' => 'get'),
                array('label' => '操作用户','display'=>false, 'as' => 'user.modify', 'action' => 'manage/User/modify', 'method' => 'get|post'),
            ),
        ),
        
        'syscontent' => array(
            'label' => '内容管理',
            'icon'  => 'fa-gears',
            'menu'  => array(
                array('label' => '分类管理','display'=>true, 'as' => 'syscontent.cat.list', 'action' => 'manage/Category/index', 'url' => '/manage/content/cat/list', 'method' => 'get'),
                array('label' => '分类操作','display'=>false, 'as' => 'syscontent.cat.modify', 'action' => 'manage/Category/modify', 'method' => 'get|post'),
                
                array('label' => '标签管理','display'=>true, 'as' => 'syscontent.flag.list', 'action' => 'manage/Flag/index', 'url' => '/manage/content/flag/list', 'method' => 'get'),
                array('label' => '标签操作','display'=>false, 'as' => 'syscontent.flag.modify', 'action' => 'manage/Flag/modify', 'method' => 'get|post'),
                
                
                
                array('label' => '文章管理','display'=>true, 'as' => 'syscontent.article.list', 'action' => 'manage/Article/index', 'url' => '/manage/content/article/list', 'method' => 'get'),
                array('label' => '文章操作','display'=>false, 'as' => 'syscontent.article.modify', 'action' => 'manage/Article/modify', 'method' => 'get|post'),
                array('label' => '文章状态','display'=>false, 'as' => 'syscontent.article.oper', 'action' => 'manage/Article/changeStatus', 'method' => 'post'),
                
                
                array('label' => '图集管理','display'=>true, 'as' => 'syscontent.atlas.list', 'action' => 'manage/Atlas/index', 'url' => '/manage/content/atlas/list', 'method' => 'get'),
                array('label' => '图集操作','display'=>false, 'as' => 'syscontent.atlas.modify', 'action' => 'manage/Atlas/modify', 'method' => 'get|post'),
                array('label' => '图集状态','display'=>false, 'as' => 'syscontent.atlas.oper', 'action' => 'manage/Atlas/changeStatus', 'method' => 'post'),
                
                
                array('label' => '评论管理','display'=>true, 'as' => 'syscontent.comment.list', 'action' => 'manage/Comment/index', 'url' => '/manage/content/comment/list', 'method' => 'get'),
                
            ),
        ),
        'accessory' => array(
            'label' => '附件管理',
            'icon'  => 'fa-gears',
            'menu'  => array(
                array('label' => '文件管理','display'=>true, 'as' => 'accessory.file.list', 'action' => 'manage/accessory/file', 'url' => '/manage/accessory/list', 'method' => 'get'),
            ),
        ),
    ],
];